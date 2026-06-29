<?php

namespace App\Imports;

use App\Models\Subject;
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
                'code' => 'required|string|max:50|unique:subjects,code',
            ]);

            if ($validator->fails()) {
                $this->errors[] = "Row {$rowNumber}: " . implode(', ', $validator->errors()->all());
                $this->skipped++;
                continue;
            }

            // Normalize type
            $type = $this->normalizeType($row['type'] ?? 'Core');

            // Check for duplicate by code
            if (Subject::where('code', $row['code'])->exists()) {
                $this->errors[] = "Row {$rowNumber}: Subject with code '{$row['code']}' already exists. Skipped.";
                $this->skipped++;
                continue;
            }

            DB::beginTransaction();
            try {
                Subject::create([
                    'name' => trim($row['name']),
                    'code' => strtoupper(trim($row['code'])),
                    'description' => $row['description'] ?? null,
                    'type' => $type,
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

    private function normalizeType($type): string
    {
        $type = strtolower(trim($type));
        
        $typeMap = [
            'core' => 'Core',
            'compulsory' => 'Core',
            'elective' => 'Elective',
            'optional' => 'Elective',
            'optional' => 'Optional',
        ];

        return $typeMap[$type] ?? 'Core';
    }
}
