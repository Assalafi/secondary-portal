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

    private $classArmId;
    private $teacherId;

    public function __construct($classArmId = null, $teacherId = null)
    {
        $this->classArmId = $classArmId;
        $this->teacherId = $teacherId;
    }

    public function collection(Collection $rows)
    {
        foreach ($rows as $index => $row) {
            $rowNumber = $index + 2; // +2 because of header row and 0-indexing

            // Skip completely empty rows
            if (!isset($row['name'])) {
                continue;
            }

            // Validate required fields
            $validator = Validator::make($row->toArray(), [
                'name' => 'required|string|max:255',
            ]);

            if ($validator->fails()) {
                $this->errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                $this->skipped++;
                continue;
            }

            DB::beginTransaction();
            try {
                // Generate unique subject code from name
                $code = $this->generateCode(trim($row['name']));

                $subject = Subject::create([
                    'name' => trim($row['name']),
                    'code' => $code,
                    'type' => 'Core',
                ]);

                // Optional: attach to class arm with teacher if provided
                if ($this->classArmId) {
                    if (!$subject->classArms()->where('class_arm_id', $this->classArmId)->exists()) {
                        $subject->classArms()->attach($this->classArmId, [
                            'teacher_id' => $this->teacherId,
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

    private function generateCode(string $name): string
    {
        // Generate code from first 3 letters of each word (max 3 words)
        $words = explode(' ', $name);
        $code = '';
        foreach (array_slice($words, 0, 3) as $word) {
            $code .= strtoupper(substr($word, 0, 3));
        }

        // Ensure uniqueness
        $baseCode = $code;
        $counter = 1;
        while (Subject::where('code', $code)->exists()) {
            $code = $baseCode . $counter;
            $counter++;
        }

        return $code;
    }
}
