<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Laporan Absensi</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f3f4f6; font-weight: bold; }
        h1 { text-align: center; color: #1f2937; }
        .header { text-align: center; margin-bottom: 30px; }
        .status-hadir { color: #059669; font-weight: bold; }
        .status-terlambat { color: #d97706; font-weight: bold; }
        .status-alpha { color: #dc2626; font-weight: bold; }
    </style>
</head>
<body>
    <div class="header">
        <h1>LAPORAN ABSENSI SISWA</h1>
        <p>{{ now()->isoFormat('D MMMM Y') }}</p>
    </div>

    <table>
        <thead>
            <tr>
                <th width="10%">No</th>
                <th width="15%">Tanggal</th>
                <th width="15%">NISN</th>
                <th width="25%">Nama</th>
                <th width="10%">Kelas</th>
                <th width="10%">Jam Masuk</th>
                <th width="15%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($attendances as $index => $attendance)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $attendance->date->format('d/m/Y') }}</td>
                    <td>{{ $attendance->user->nisn }}</td>
                    <td>{{ $attendance->user->name }}</td>
                    <td>{{ $attendance->user->class }}</td>
                    <td>{{ $attendance->check_in ? $attendance->check_in->format('H:i') : '-' }}</td>
                    <td class="status-{{ $attendance->status }}">
                        {{ strtoupper($attendance->status) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>