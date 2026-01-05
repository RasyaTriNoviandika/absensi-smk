<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsTemplateExport implements FromArray, WithHeadings, WithStyles
{
    public function array(): array
    {
        return [
            [
                '1234567890',
                'Tompel ',
                '10 DKV 1',
                '081234567890',
                'budi@example.com'
            ],
            [
                '1234567891',
                'Engkus Aminah',
                '11 SIJA 2',
                '081234567891',
                'siti@example.com'
            ],
        ];
    }

    public function headings(): array
    {
        return [
            'NISN (10 digit) *',
            'Nama Lengkap *',
            'Kelas (contoh: 10 DKV 1) *',
            'No. HP (opsional)',
            'Email (opsional)'
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'size' => 12],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'color' => ['rgb' => 'E3F2FD']
                ]
            ],
        ];
    }
}