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
            ['Mathematics', 'MAT', 'Core mathematical concepts', 'Core'],
            ['English Language', 'ENG', 'English language and literature', 'Core'],
            ['Physics', 'PHY', 'Study of matter and energy', 'Core'],
            ['Chemistry', 'CHE', 'Study of chemicals and reactions', 'Core'],
            ['Biology', 'BIO', 'Study of living organisms', 'Core'],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
            'code',
            'description',
            'type',
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
        ];
    }
}
