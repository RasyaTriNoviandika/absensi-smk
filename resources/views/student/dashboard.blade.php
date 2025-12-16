@extends('layouts.simple')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 1rem;">
    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 1rem; margin-bottom: 2rem;">
        <div style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #10b981;">
            <h3 style="font-size: 2rem; margin-bottom: 0.5rem;">{{ $stats['hadir'] }}</h3>
            <p style="color: #666;">Hadir Bulan Ini</p>
        </div>
        <div style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #f59e0b;">
            <h3 style="font-size: 2rem; margin-bottom: 0.5rem;">{{ $stats['terlambat'] }}</h3>
            <p style="color: #666;">Terlambat Bulan Ini</p>
        </div>
        <div style="background: white; padding: 1.5rem; border-radius: 8px; border-left: 4px solid #ef4444;">
            <h3 style="font-size: 2rem; margin-bottom: 0.5rem;">{{ $stats['alpha'] }}</h3>
            <p style="color: #666;">Alpha Bulan Ini</p>
        </div>
    </div>

    <!-- Attendance Actions -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 2rem;">
        <div style="background: white; padding: 2rem; border-radius: 8px;">
            <h2 style="margin-bottom: 1rem;">Absen Masuk</h2>
            @if($todayAttendance && $todayAttendance->check_in)
                <div style="background: #d1fae5; padding: 1rem; border-radius: 6px; color: #065f46;">
                    ✓ Sudah Absen: {{ $todayAttendance->check_in->format('H:i') }}
                </div>
            @else
                <button onclick="checkIn()" style="width: 100%; padding: 1rem; background: #3b82f6; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    📷 Scan Wajah
                </button>
            @endif
        </div>

        <div style="background: white; padding: 2rem; border-radius: 8px;">
            <h2 style="margin-bottom: 1rem;">Absen Pulang</h2>
            @if($todayAttendance && $todayAttendance->check_out)
                <div style="background: #e0e7ff; padding: 1rem; border-radius: 6px; color: #3730a3;">
                    ✓ Sudah Absen: {{ $todayAttendance->check_out->format('H:i') }}
                </div>
            @elseif($todayAttendance)
                <button onclick="checkOut()" style="width: 100%; padding: 1rem; background: #8b5cf6; color: white; border: none; border-radius: 6px; cursor: pointer;">
                    📷 Scan Wajah
                </button>
            @else
                <button disabled style="width: 100%; padding: 1rem; background: #d1d5db; border: none; border-radius: 6px;">
                    Absen masuk dulu
                </button>
            @endif
        </div>
    </div>

    <!-- History Table -->
    <div style="background: white; padding: 1.5rem; border-radius: 8px;">
        <h2 style="margin-bottom: 1rem;">Riwayat 7 Hari</h2>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f9fafb;">
                    <th style="padding: 0.75rem; text-align: left;">Tanggal</th>
                    <th style="padding: 0.75rem; text-align: left;">Masuk</th>
                    <th style="padding: 0.75rem; text-align: left;">Pulang</th>
                    <th style="padding: 0.75rem; text-align: left;">Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($recentAttendances as $att)
                <tr>
                    <td style="padding: 0.75rem;">{{ $att->date->format('d/m/Y') }}</td>
                    <td style="padding: 0.75rem;">{{ $att->check_in ? $att->check_in->format('H:i') : '-' }}</td>
                    <td style="padding: 0.75rem;">{{ $att->check_out ? $att->check_out->format('H:i') : '-' }}</td>
                    <td style="padding: 0.75rem;">
                        <span style="padding: 0.3rem 0.8rem; border-radius: 20px; font-size: 0.75rem; background: {{ $att->status == 'hadir' ? '#d1fae5' : ($att->status == 'terlambat' ? '#fef3c7' : '#fee2e2') }};">
                            {{ strtoupper($att->status) }}
                        </span>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<script>
async function checkIn() {
    if (!confirm('Mulai absen masuk?')) return;
    
    try {
        const stream = await navigator.mediaDevices.getUserMedia({ video: true });
        const video = document.createElement('video');
        video.srcObject = stream;
        video.play();
        
        // Wait 2 seconds for camera to stabilize
        await new Promise(resolve => setTimeout(resolve, 2000));
        
        // Capture frame
        const canvas = document.createElement('canvas');
        canvas.width = video.videoWidth;
        canvas.height = video.videoHeight;
        canvas.getContext('2d').drawImage(video, 0, 0);
        const photo = canvas.toDataURL('image/png');
        
        stream.getTracks().forEach(track => track.stop());
        
        // Send to server
        const response = await fetch('{{ route("attendance.checkin") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                face_descriptor: Array(128).fill(0), // Dummy descriptor
                photo: photo
            })
        });
        
        const result = await response.json();
        alert(result.message);
        
        if (result.success) {
            location.reload();
        }
    } catch (err) {
        alert('Error: ' + err.message);
    }
}

async function checkOut() {
    // Similar to checkIn but use checkout route
    // Implementation sama seperti checkIn
}
</script>
@endsection