<?php
namespace App\Exports;

use App\Models\Attendance;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class AttendanceExport implements FromCollection, WithHeadings, WithMapping
{
    protected $filters;

    public function __construct($filters)
    {
        $this->filters = $filters;
    }

    public function collection()
    {
        $query = Attendance::with('user');

        if (isset($this->filters['start_date']) && isset($this->filters['end_date'])) {
            $query->whereBetween('date', [$this->filters['start_date'], $this->filters['end_date']]);
        }

        if (isset($this->filters['class'])) {
            $query->whereHas('user', function($q) {
                $q->where('class', $this->filters['class']);
            });
        }

        return $query->orderBy('date', 'desc')->get();
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama',
            'Kelas',
            'Tanggal',
            'Jam Masuk',
            'Jam Pulang',
            'Status',
        ];
    }

    public function map($attendance): array
    {
        return [
            $attendance->user->nisn,
            $attendance->user->name,
            $attendance->user->class,
            $attendance->date->format('d/m/Y'),
            $attendance->check_in ? $attendance->check_in->format('H:i') : '-',
            $attendance->check_out ? $attendance->check_out->format('H:i') : '-',
            strtoupper($attendance->status),
        ];
    }
}
