<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentTemplateExport implements FromArray, WithHeadings, WithStyles, WithColumnWidths
{
    public function array(): array
    {
        // Sample data rows
        return [
            ['John', 'Doe', 'Michael', 'Male', '2010-05-15', 'Lagos', 'Ikeja', 'Nigerian', 'Lagos', 'Ikeja', 'Ikeja', '', 'john@example.com', '2024-09-01'],
            ['Jane', 'Smith', '', 'Female', '2011-03-20', 'Abuja', 'AMAC', 'Nigerian', 'FCT', 'AMAC', 'Wuse', '', '', '2024-09-01'],
        ];
    }

    public function headings(): array
    {
        return [
            'first_name',
            'surname',
            'middle_name',
            'gender',
            'date_of_birth',
            'state_of_origin',
            'lga',
            'nationality',
            'place_of_birth_state',
            'place_of_birth_lga',
            'place_of_birth_town',
            'admission_no',
            'email',
            'admission_date',
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
            'A' => 15,
            'B' => 15,
            'C' => 15,
            'D' => 10,
            'E' => 15,
            'F' => 18,
            'G' => 15,
            'H' => 15,
            'I' => 18,
            'J' => 18,
            'K' => 18,
            'L' => 18,
            'M' => 25,
            'N' => 15,
        ];
    }
}
