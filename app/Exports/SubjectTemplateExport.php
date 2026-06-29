<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SubjectTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Sample data rows
        return [
            ['Mathematics', 'MAT', 'Core mathematical concepts', 'Core', 'JSS', 'JSS 1', '', 'A', ''],
            ['English Language', 'ENG', 'English language and literature', 'Core', 'JSS', 'JSS 1', '', 'A', ''],
            ['Physics', '', 'Study of matter and energy', 'Core', 'SSS', 'SSS 1', 'Science', 'A', ''],
            ['Chemistry', '', 'Study of chemicals and reactions', 'Core', 'SSS', 'SSS 1', 'Science', 'A', ''],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'code',
            'description',
            'type',
            'level',
            'class_name',
            'group',
            'arm',
            'teacher_id',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 11],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['argb' => 'FFE2E8F0'],
                ],
            ],
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 30,
            'B' => 15,
            'C' => 40,
            'D' => 15,
            'E' => 15,
            'F' => 15,
            'G' => 15,
            'H' => 15,
            'I' => 15,
        ];
    }
}
