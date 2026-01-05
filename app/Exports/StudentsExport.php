<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class StudentsExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $search;
    protected $class;
    protected $status;

    public function __construct($search = null, $class = null, $status = 'approved')
    {
        $this->search = $search;
        $this->class = $class;
        $this->status = $status;
    }

    public function collection()
    {
        $query = User::where('role', 'student');

        if ($this->status) {
            if ($this->status === 'approved') {
                $query->where('status', 'approved');
            } elseif ($this->status === 'pending') {
                $query->where('status', 'pending');
            } elseif ($this->status === 'rejected') {
                $query->where('status', 'rejected');
            }
        }

        if ($this->class) {
            $query->where('class', $this->class);
        }

        if ($this->search) {
            $query->where(function($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('nisn', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy('class')->orderBy('name')->get();
    }

    public function headings(): array
    {
        return [
            'NISN',
            'Nama Lengkap',
            'Username',
            'Kelas',
            'Email',
            'No. Hp',
            'Status',
            'Tanggal Daftar',
        ];
    }

    public function map($student): array
    {
        return [
            $student->nisn,
            $student->name,
            $student->username,
            $student->class,
            $student->email ?? '-',
            $student->phone ?? '-',
            strtoupper($student->status),
            $student->created_at->format('d/m/Y H:i'),
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true]],
        ];
    }
}