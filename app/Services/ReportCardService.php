<?php

namespace App\Services;

use App\Models\AcademicSession;
use App\Models\AffectiveTrait;
use App\Models\Attendance;
use App\Models\ClassArm;
use App\Models\GradingProfile;
use App\Models\PsychomotorTrait;
use App\Models\ReportCard;
use App\Models\ReportCardItem;
use App\Models\ReportSettings;
use App\Models\ScoreBatch;
use App\Models\SessionTerm;
use App\Models\Staff;
use App\Models\Student;
use App\Models\StudentAffectiveRating;
use App\Models\StudentPsychomotorRating;
use App\Models\Term;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class ReportCardService
{
    private ?ReportSettings $settings = null;

    private array $gradingProfiles = [];

    public function generate(
        Student $student,
        ClassArm $classArm,
        AcademicSession $session,
        ?Term $term,
        string $reportType = 'termly'
    ): ReportCard {
        if ($student->current_class_arm_id !== $classArm->id) {
            throw ValidationException::withMessages([
                'student' => 'The selected student is not enrolled in this class arm.',
            ]);
        }

        if ($reportType === 'termly' && ! $term) {
            throw ValidationException::withMessages([
                'term_id' => 'A term is required for a termly report card.',
            ]);
        }

        $sessionTerm = $this->resolveSessionTerm($session, $term);
        $identity = [
            'student_id' => $student->id,
            'class_id' => $classArm->school_class_id,
            'academic_session_id' => $sessionTerm->id,
            'term_id' => $reportType === 'termly' ? $sessionTerm->id : null,
            'report_type' => $reportType,
        ];
        $existingReport = ReportCard::where($identity)->first();

        if ($existingReport && in_array($existingReport->status, ['approved', 'published'], true)) {
            throw ValidationException::withMessages([
                'report_card' => 'A finalized report card already exists and cannot be regenerated.',
            ]);
        }

        $batches = $this->scoreBatches($classArm, $session, $term);
        $studentRows = $this->normalisedSubjectRows($batches, $student->id, $classArm->schoolClass->level);

        if ($studentRows->isEmpty()) {
            throw ValidationException::withMessages([
                'scores' => 'No scores were found for this student in the selected period.',
            ]);
        }

        $classStudentIds = $classArm->students()->pluck('students.id');
        $subjectStatistics = $this->subjectStatistics(
            $batches,
            $classArm->schoolClass->level,
            $classStudentIds
        );
        $attendance = $this->attendanceSummary($student, $classArm, $sessionTerm, $session, $reportType);

        return DB::transaction(function () use (
            $student,
            $classArm,
            $sessionTerm,
            $studentRows,
            $subjectStatistics,
            $attendance,
            $identity
        ) {
            $total = round($studentRows->sum('total_score'), 2);
            $maximum = $studentRows->count() * 100;
            $average = $maximum > 0 ? round(($total / $maximum) * 100, 2) : 0;
            $grade = $this->gradeFor($average, $classArm->schoolClass->level);
            $classTeacherId = $classArm->class_teacher_id
                ? Staff::where('user_id', $classArm->class_teacher_id)->value('id')
                : null;

            $reportCard = ReportCard::firstOrNew($identity);

            $reportCard->fill([
                'status' => 'draft',
                'total_score' => $total,
                'maximum_score' => $maximum,
                'average_score' => $average,
                'final_grade' => $grade['grade'],
                'final_remark' => $grade['remark'],
                'class_teacher_id' => $classTeacherId,
                'attendance_opened' => $attendance['opened'],
                'attendance_present' => $attendance['present'],
                'attendance_absent' => $attendance['absent'],
                'attendance_late' => $attendance['late'],
                'attendance_percentage' => $attendance['percentage'],
                'verification_code' => $reportCard->verification_code
                    ?: $this->verificationCode($student, $sessionTerm),
            ])->save();

            $reportCard->items()->delete();

            foreach ($studentRows as $row) {
                $stats = $subjectStatistics->get($row['subject_id'], []);

                ReportCardItem::create([
                    'report_card_id' => $reportCard->id,
                    'subject_id' => $row['subject_id'],
                    'subject_name' => $row['subject_name'],
                    'ca_score' => $row['ca_score'],
                    'exam_score' => $row['exam_score'],
                    'total_score' => $row['total_score'],
                    'grade' => $row['grade'],
                    'grade_point' => $row['grade_point'],
                    'remark' => $row['remark'],
                    'subject_position' => $stats['positions'][$student->id] ?? null,
                    'class_average' => $stats['average'] ?? null,
                    'highest_score' => $stats['highest'] ?? null,
                    'lowest_score' => $stats['lowest'] ?? null,
                ]);
            }

            $this->initialiseDomainRatings($reportCard);
            $this->recalculateCohort($reportCard, $classArm);

            return $reportCard->fresh($this->reportRelations());
        });
    }

    public function generateBulk(
        ClassArm $classArm,
        AcademicSession $session,
        ?Term $term,
        string $reportType,
        ?array $studentIds = null
    ): array {
        $students = $classArm->students()
            ->when($studentIds, fn ($query) => $query->whereIn('id', $studentIds))
            ->orderBy('surname')
            ->orderBy('first_name')
            ->get();

        $generated = 0;
        $skipped = [];

        foreach ($students as $student) {
            try {
                $this->generate($student, $classArm, $session, $term, $reportType);
                $generated++;
            } catch (ValidationException $exception) {
                $skipped[] = $student->full_name.': '.collect($exception->errors())->flatten()->first();
            }
        }

        return compact('generated', 'skipped');
    }

    public function gradeFor(float $score, ?string $level): array
    {
        $settings = $this->settings();
        $profileKey = $settings->default_grading_profile_id ?: "level:{$level}";

        if (! array_key_exists($profileKey, $this->gradingProfiles)) {
            $profile = $settings->default_grading_profile_id
                ? GradingProfile::active()->with('scales')->find($settings->default_grading_profile_id)
                : null;

            $profile ??= GradingProfile::query()
                ->where('level', $level)
                ->where('is_default', true)
                ->where('status', 'active')
                ->with('scales')
                ->first();

            $this->gradingProfiles[$profileKey] = $profile;
        }

        $profile = $this->gradingProfiles[$profileKey];

        $scale = $profile?->scales
            ->first(fn ($item) => $score >= (float) $item->min_score && $score <= (float) $item->max_score);

        if ($scale) {
            return [
                'grade' => $scale->grade,
                'remark' => $scale->remark,
                'grade_point' => (float) $scale->grade_point,
            ];
        }

        if ($level === 'SS') {
            return match (true) {
                $score >= 75 => ['grade' => 'A1', 'remark' => 'Excellent', 'grade_point' => 5],
                $score >= 70 => ['grade' => 'B2', 'remark' => 'Very Good', 'grade_point' => 4.5],
                $score >= 65 => ['grade' => 'B3', 'remark' => 'Good', 'grade_point' => 4],
                $score >= 60 => ['grade' => 'C4', 'remark' => 'Credit', 'grade_point' => 3.5],
                $score >= 55 => ['grade' => 'C5', 'remark' => 'Credit', 'grade_point' => 3],
                $score >= 50 => ['grade' => 'C6', 'remark' => 'Credit', 'grade_point' => 2.5],
                $score >= 45 => ['grade' => 'D7', 'remark' => 'Pass', 'grade_point' => 2],
                $score >= 40 => ['grade' => 'E8', 'remark' => 'Pass', 'grade_point' => 1.5],
                default => ['grade' => 'F9', 'remark' => 'Fail', 'grade_point' => 1],
            };
        }

        return match (true) {
            $score >= 70 => ['grade' => 'A', 'remark' => 'Excellent', 'grade_point' => 5],
            $score >= 60 => ['grade' => 'B', 'remark' => 'Very Good', 'grade_point' => 4],
            $score >= 50 => ['grade' => 'C', 'remark' => 'Good', 'grade_point' => 3],
            $score >= 45 => ['grade' => 'D', 'remark' => 'Fair', 'grade_point' => 2],
            $score >= 40 => ['grade' => 'E', 'remark' => 'Pass', 'grade_point' => 1.5],
            default => ['grade' => 'F', 'remark' => 'Fail', 'grade_point' => 1],
        };
    }

    public function reportRelations(): array
    {
        return [
            'student',
            'class',
            'academicSession',
            'term',
            'items.subject',
            'affectiveRatings.trait',
            'psychomotorRatings.trait',
            'classTeacher.user',
            'approvedBy',
            'publishedBy',
            'nextClass',
        ];
    }

    private function resolveSessionTerm(AcademicSession $session, ?Term $term): SessionTerm
    {
        $termName = $term?->name;

        $query = SessionTerm::where('academic_year', $session->name);
        $sessionTerm = $termName
            ? (clone $query)->where('term_name', $termName)->first()
            : (clone $query)->orderBy('id')->first();

        if ($sessionTerm) {
            return $sessionTerm;
        }

        if (! $termName) {
            throw ValidationException::withMessages([
                'session_id' => 'No configured term was found for the selected academic session.',
            ]);
        }

        return SessionTerm::create([
            'academic_year' => $session->name,
            'term_name' => $termName,
            'start_date' => $session->start_date,
            'end_date' => $session->end_date,
            'is_current' => (bool) $session->is_current,
            'status' => 'Active',
        ]);
    }

    private function scoreBatches(ClassArm $classArm, AcademicSession $session, ?Term $term): Collection
    {
        return ScoreBatch::query()
            ->where('class_id', $classArm->school_class_id)
            ->where('academic_session_id', $session->id)
            ->when($term, fn ($query) => $query->where('term_id', $term->id))
            ->with(['subject', 'scores'])
            ->get();
    }

    private function normalisedSubjectRows(Collection $batches, int $studentId, ?string $level): Collection
    {
        $settings = $this->settings();

        return $batches
            ->map(function ($batch) use ($studentId, $level, $settings) {
                $score = $batch->scores->firstWhere('student_id', $studentId);
                if (! $score || ! $batch->subject) {
                    return null;
                }

                $ca = (float) $score->first_ca + (float) $score->second_ca + (float) $score->third_ca;
                $caMax = (float) $batch->first_ca_max + (float) $batch->second_ca_max + (float) $batch->third_ca_max;
                $exam = (float) $score->exam;
                $examMax = (float) $batch->exam_max;

                $normalisedCa = $caMax > 0 ? ($ca / $caMax) * $settings->ca_max_score : 0;
                $normalisedExam = $examMax > 0 ? ($exam / $examMax) * $settings->exam_max_score : 0;
                $total = min(100, max(0, $normalisedCa + $normalisedExam));
                $grade = $this->gradeFor($total, $level);

                return [
                    'subject_id' => $batch->subject_id,
                    'subject_name' => $batch->subject->name,
                    'ca_score' => $normalisedCa,
                    'exam_score' => $normalisedExam,
                    'total_score' => $total,
                    'grade' => $grade['grade'],
                    'remark' => $grade['remark'],
                    'grade_point' => $grade['grade_point'],
                ];
            })
            ->filter()
            ->groupBy('subject_id')
            ->map(function (Collection $rows) use ($level) {
                $first = $rows->first();
                $ca = round($rows->avg('ca_score'), 2);
                $exam = round($rows->avg('exam_score'), 2);
                $total = round($rows->avg('total_score'), 2);
                $grade = $this->gradeFor($total, $level);

                return array_merge($first, [
                    'ca_score' => $ca,
                    'exam_score' => $exam,
                    'total_score' => $total,
                    'grade' => $grade['grade'],
                    'remark' => $grade['remark'],
                    'grade_point' => $grade['grade_point'],
                ]);
            })
            ->sortBy('subject_name')
            ->values();
    }

    private function subjectStatistics(Collection $batches, ?string $level, Collection $classStudentIds): Collection
    {
        $studentIds = $batches->flatMap->scores
            ->pluck('student_id')
            ->intersect($classStudentIds)
            ->unique();
        $rowsByStudent = $studentIds->mapWithKeys(
            fn ($studentId) => [$studentId => $this->normalisedSubjectRows($batches, $studentId, $level)]
        );

        return $rowsByStudent
            ->flatMap(fn (Collection $rows, $studentId) => $rows->map(
                fn (array $row) => array_merge($row, ['student_id' => $studentId])
            ))
            ->groupBy('subject_id')
            ->map(function (Collection $rows) {
                $sorted = $rows->sortByDesc('total_score')->values();
                $positions = [];
                $lastScore = null;
                $lastPosition = 0;

                foreach ($sorted as $index => $row) {
                    $score = round((float) $row['total_score'], 2);
                    $position = $lastScore !== null && $score === $lastScore ? $lastPosition : $index + 1;
                    $positions[$row['student_id']] = $position;
                    $lastScore = $score;
                    $lastPosition = $position;
                }

                return [
                    'average' => round($rows->avg('total_score'), 2),
                    'highest' => round($rows->max('total_score'), 2),
                    'lowest' => round($rows->min('total_score'), 2),
                    'positions' => $positions,
                ];
            });
    }

    private function attendanceSummary(
        Student $student,
        ClassArm $classArm,
        SessionTerm $sessionTerm,
        AcademicSession $session,
        string $reportType
    ): array {
        $start = $reportType === 'annual' ? $session->start_date : ($sessionTerm->start_date ?: $session->start_date);
        $end = $reportType === 'annual' ? $session->end_date : ($sessionTerm->end_date ?: $session->end_date);

        $base = Attendance::where('class_arm_id', $classArm->id)
            ->when($start, fn ($query) => $query->whereDate('date', '>=', $start))
            ->when($end, fn ($query) => $query->whereDate('date', '<=', $end));

        $opened = (clone $base)->distinct()->count('date');
        $studentAttendance = (clone $base)->where('student_id', $student->id)->get();
        $present = $studentAttendance->whereIn('status', ['Present', 'Late'])->count();
        $absent = $studentAttendance->where('status', 'Absent')->count();
        $late = $studentAttendance->where('status', 'Late')->count();

        return [
            'opened' => $opened,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'percentage' => $opened > 0 ? round(($present / $opened) * 100, 2) : 0,
        ];
    }

    private function initialiseDomainRatings(ReportCard $reportCard): void
    {
        foreach (AffectiveTrait::active()->ordered()->get() as $trait) {
            StudentAffectiveRating::firstOrCreate(
                ['report_card_id' => $reportCard->id, 'trait_id' => $trait->id],
                ['student_id' => $reportCard->student_id, 'rating_value' => 3]
            );
        }

        foreach (PsychomotorTrait::active()->ordered()->get() as $trait) {
            StudentPsychomotorRating::firstOrCreate(
                ['report_card_id' => $reportCard->id, 'trait_id' => $trait->id],
                ['student_id' => $reportCard->student_id, 'rating_value' => 3]
            );
        }
    }

    private function recalculateCohort(ReportCard $reportCard, ClassArm $classArm): void
    {
        $reports = ReportCard::query()
            ->where('class_id', $reportCard->class_id)
            ->whereIn('student_id', $classArm->students()->select('students.id'))
            ->where('academic_session_id', $reportCard->academic_session_id)
            ->where('report_type', $reportCard->report_type)
            ->when(
                $reportCard->term_id,
                fn ($query) => $query->where('term_id', $reportCard->term_id),
                fn ($query) => $query->whereNull('term_id')
            )
            ->orderByDesc('average_score')
            ->orderBy('student_id')
            ->get();

        $highest = $reports->max('average_score');
        $lowest = $reports->min('average_score');
        $average = $reports->avg('average_score');
        $lastScore = null;
        $lastPosition = 0;

        foreach ($reports as $index => $item) {
            $score = round((float) $item->average_score, 2);
            $position = $lastScore !== null && $score === $lastScore ? $lastPosition : $index + 1;

            $item->update([
                'class_position' => $position,
                'number_in_class' => $reports->count(),
                'class_highest_average' => $highest,
                'class_lowest_average' => $lowest,
                'class_average' => $average,
            ]);

            $lastScore = $score;
            $lastPosition = $position;
        }
    }

    private function verificationCode(Student $student, SessionTerm $sessionTerm): string
    {
        do {
            $code = sprintf(
                'RPT-%s-%s-%s',
                $sessionTerm->id,
                $student->id,
                strtoupper(Str::random(8))
            );
        } while (ReportCard::where('verification_code', $code)->exists());

        return $code;
    }

    private function settings(): ReportSettings
    {
        return $this->settings ??= ReportSettings::getSettings();
    }
}
