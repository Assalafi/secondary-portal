<?php

namespace App\Imports;

use App\Models\Subject;
use App\Models\SchoolClass;
use App\Models\ClassArm;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class SubjectsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public $imported = 0;
    public $skipped = 0;
    public $errors = [];

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-indexing

            // Skip completely empty rows
            if (!isset($row['name']) && !isset($row['code'])) {
                continue;
            }

            // Validate required fields
            $validator = Validator::make($row->toArray(), [
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:20',
            ]);

            if ($validator->fails()) {
                $this->errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                $this->skipped++;
                continue;
            }

            // Normalize type
            $type = $this->normalizeType($row['type'] ?? 'Core');

            // Generate code if not provided
            $code = $row['code'];
            if (empty($code)) {
                $prefix = isset($row['level']) && !empty($row['level'])
                    ? strtoupper(substr($row['level'], 0, 1))
                    : 'S';
                $last = Subject::where('code', 'like', $prefix . '-%')->orderBy('code', 'desc')->value('code');
                $nextNum = 1;
                if ($last && preg_match('/^' . preg_quote($prefix, '/') . '-(\d{2,})$/', $last, $m)) {
                    $nextNum = intval($m[1]) + 1;
                }
                $code = $prefix . '-' . str_pad((string) $nextNum, 2, '0', STR_PAD_LEFT);
            } else {
                $code = strtoupper(trim($code));
            }

            // Check for duplicate by code
            if (Subject::where('code', $code)->exists()) {
                $this->errors[] = "Row {$rowNumber}: Subject with code '{$code}' already exists. Skipped.";
                $this->skipped++;
                continue;
            }

            DB::beginTransaction();
            try {
                $subject = Subject::create([
                    'name' => trim($row['name']),
                    'code' => $code,
                    'description' => $row['description'] ?? null,
                    'type' => $type,
                ]);

                // Optional: attach to class arm with teacher
                if (!empty($row['level']) && !empty($row['class_name']) && !empty($row['arm'])) {
                    $schoolClass = SchoolClass::firstOrCreate([
                        'level' => $row['level'],
                        'name' => $row['class_name'],
                        'group' => $row['group'] ?? null,
                    ], [
                        'status' => 'Active',
                    ]);

                    $classArm = ClassArm::firstOrCreate([
                        'school_class_id' => $schoolClass->id,
                        'name' => $row['arm'],
                    ]);

                    if (!$subject->classArms()->where('class_arm_id', $classArm->id)->exists()) {
                        $teacherId = null;
                        if (!empty($row['teacher_id'])) {
                            $teacher = User::find($row['teacher_id']);
                            if ($teacher) {
                                $teacherId = $teacher->id;
                            }
                        }

                        $subject->classArms()->attach($classArm->id, [
                            'teacher_id' => $teacherId,
                        ]);
                    }
                }

                DB::commit();
                $this->imported++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                $this->skipped++;
            }
        }
    }

    private function normalizeType($type): string
    {
        $type = strtolower(trim($type));
        
        $typeMap = [
            'core' => 'Core',
            'compulsory' => 'Core',
            'elective' => 'Elective',
            'optional' => 'Elective',
        ];

        return $typeMap[$type] ?? 'Core';
    }
}
