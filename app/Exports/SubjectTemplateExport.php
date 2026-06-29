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
            ['Mathematics'],
            ['English Language'],
            ['Physics'],
            ['Chemistry'],
            ['Biology'],
            ['Geography'],
            ['Economics'],
            ['Civic Education'],
            ['Computer Science'],
            ['French'],
        ];
    }

    public function headings(): array
    {
        return [
            'name',
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
            'A' => 40,
        ];
    }
}
