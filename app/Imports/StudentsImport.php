<?php

namespace App\Imports;

use App\Models\Student;
use App\Models\User;
use App\Models\Role;
use App\Models\ClassArm;
use App\Models\AcademicSession;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;

class StudentsImport implements ToCollection, WithHeadingRow, SkipsEmptyRows
{
    public $imported = 0;
    public $skipped = 0;
    public $errors = [];

    private $classArmId;
    private $academicSessionId;

    public function __construct($classArmId, $academicSessionId)
    {
        $this->classArmId = $classArmId;
        $this->academicSessionId = $academicSessionId;
    }

    public function collection(Collection $rows)
    {
        $studentRole = Role::where('name', 'Student')->first();
        $roleId = $studentRole ? $studentRole->id : 5;

        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-indexing

            // Skip completely empty rows
            if (!isset($row['first_name']) && !isset($row['surname'])) {
                continue;
            }

            // Validate required fields
            $validator = Validator::make($row->toArray(), [
                'first_name' => 'required|string|max:255',
                'surname' => 'required|string|max:255',
                'gender' => 'required|in:Male,Female,male,female,M,F,m,f',
                'date_of_birth' => 'required',
            ]);

            if ($validator->fails()) {
                $this->errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                $this->skipped++;
                continue;
            }

            // Normalize gender
            $gender = $this->normalizeGender($row['gender']);

            // Parse date of birth
            $dob = $this->parseDate($row['date_of_birth'] ?? null);
            if (!$dob) {
                $this->errors[] = "Row {$rowNumber}: Invalid date of birth format.";
                $this->skipped++;
                continue;
            }

            // Parse admission date
            $admissionDate = $this->parseDate($row['admission_date'] ?? null) ?: now()->format('Y-m-d');

            // Check for duplicate by admission number if provided
            if (!empty($row['admission_no'])) {
                $existingStudent = Student::where('admission_no', $row['admission_no'])->first();
                if ($existingStudent) {
                    $this->errors[] = "Row {$rowNumber}: Student with admission no '{$row['admission_no']}' already exists. Skipped.";
                    $this->skipped++;
                    continue;
                }
            }

            DB::beginTransaction();
            try {
                // Generate admission number
                $admissionNumber = !empty($row['admission_no']) ? $row['admission_no'] : $this->generateAdmissionNumber();

                // Generate email
                $email = !empty($row['email']) ? $row['email'] : strtolower($admissionNumber) . '@student.portal.com';

                // Check if email already exists
                if (User::where('email', $email)->exists()) {
                    $email = strtolower($admissionNumber) . '_' . time() . '@student.portal.com';
                }

                // Create user account
                $user = User::create([
                    'name' => trim($row['first_name'] . ' ' . ($row['surname'] ?? '')),
                    'email' => $email,
                    'password' => Hash::make($admissionNumber),
                    'role_id' => $roleId,
                    'status' => 'Active',
                ]);

                // Create student record
                Student::create([
                    'user_id' => $user->id,
                    'admission_no' => $admissionNumber,
                    'surname' => $row['surname'],
                    'first_name' => $row['first_name'],
                    'middle_name' => $row['middle_name'] ?? null,
                    'gender' => $gender,
                    'dob' => $dob,
                    'state_of_origin' => $row['state_of_origin'] ?? null,
                    'lga' => $row['lga'] ?? null,
                    'nationality' => $row['nationality'] ?? 'Nigerian',
                    'place_of_birth_state' => $row['place_of_birth_state'] ?? null,
                    'place_of_birth_lga' => $row['place_of_birth_lga'] ?? null,
                    'place_of_birth_town' => $row['place_of_birth_town'] ?? null,
                    'current_class_arm_id' => $this->classArmId,
                    'academic_session_id' => $this->academicSessionId,
                    'admission_date' => $admissionDate,
                    'status' => 'Active',
                ]);

                DB::commit();
                $this->imported++;
            } catch (\Exception $e) {
                DB::rollBack();
                $this->errors[] = "Row {$rowNumber}: " . $e->getMessage();
                $this->skipped++;
            }
        }
    }

    private function normalizeGender($gender): string
    {
        $gender = strtolower(trim($gender));
        if (in_array($gender, ['male', 'm'])) {
            return 'Male';
        }
        return 'Female';
    }

    private function parseDate($date): ?string
    {
        if (empty($date)) {
            return null;
        }

        // Handle Excel serial date numbers
        if (is_numeric($date)) {
            try {
                $unixDate = ($date - 25569) * 86400;
                return date('Y-m-d', $unixDate);
            } catch (\Exception $e) {
                return null;
            }
        }

        // Try common date formats
        $formats = ['Y-m-d', 'd/m/Y', 'm/d/Y', 'd-m-Y', 'Y/m/d'];
        foreach ($formats as $format) {
            try {
                $parsed = \DateTime::createFromFormat($format, $date);
                if ($parsed && $parsed->format($format) === $date) {
                    return $parsed->format('Y-m-d');
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        // Try strtotime as fallback
        $timestamp = strtotime($date);
        if ($timestamp) {
            return date('Y-m-d', $timestamp);
        }

        return null;
    }

    private function generateAdmissionNumber(): string
    {
        $year = date('Y');
        $prefix = 'SSP' . $year;

        $lastStudent = Student::where('admission_no', 'like', $prefix . '%')
            ->orderBy('admission_no', 'desc')
            ->first();

        if ($lastStudent) {
            $lastNumber = (int) substr($lastStudent->admission_no, -4);
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }
}
