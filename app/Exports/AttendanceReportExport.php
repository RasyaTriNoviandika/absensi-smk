<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceReportExport implements FromCollection, WithHeadings, WithMapping
{
    protected $reportData;

    public function __construct($reportData)
    {
        $this->reportData = $reportData;
    }

    public function collection()
    {
        return collect($this->reportData);
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama',
            'Kelas',
            'Hadir',
            'Terlambat',
            'Alpha',
            'Persentase (%)',
        ];
    }

    public function map($row): array
    {
        return [
            $row['student']->nisn,
            $row['student']->name,
            $row['student']->class,
            $row['hadir'],
            $row['terlambat'],
            $row['alpha'],
            $row['percentage'],
        ];
    }
}