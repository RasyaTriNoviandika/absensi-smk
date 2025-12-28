<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\AttendanceExport;
use Barryvdh\DomPDF\Facade\Pdf;

class AdminController extends Controller
{
    public function dashboard()
    {
        $date = today()->format('Y-m-d');
        $classes = $this->getAvailableClasses();

        $totalStudents = User::where('role', 'student')
            ->where('status', 'approved')
            ->count();

        $todayStats = Attendance::whereDate('date', today())
            ->selectRaw('status, COUNT(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status');

        $stats = [
            'total_students' => $totalStudents,
            'present_today' => $todayStats->get('hadir', 0),
            'late_today' => $todayStats->get('terlambat', 0),
            'alpha_today' => $totalStudents - $todayStats->sum(),
            'pending_approval' => User::where('role', 'student')
                ->where('status', 'pending')
                ->count(),
        ];

        $weeklyDataRaw = Attendance::selectRaw('DATE(date) as date, COUNT(*) as count')
            ->where('date', '>=', today()->subDays(6))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->keyBy('date');

        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $d = today()->subDays($i);
            $key = $d->format('Y-m-d');

            $weeklyData[] = [
                'date' => $d->format('d M'),
                'count' => $weeklyDataRaw->get($key)->count ?? 0
            ];
        }

        $recentAttendances = Attendance::with(['user:id,name,nisn,class'])
            ->select('id', 'user_id', 'status', 'created_at')
            ->latest()
            ->limit(10)
            ->get();

        $monitoringData = collect();

        return view('admin.dashboard', compact(
            'stats',
            'weeklyData',
            'recentAttendances',
            'date',
            'classes',
            'monitoringData'
        ));
    }

    public function approvals()
    {
        $pendingUsers = User::students()
            ->pending()
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.approvals', compact('pendingUsers'));
    }

    public function approve(User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'User sudah di-approve atau reject.');
        }

        $user->update(['status' => 'approved']);

        return back()->with('success', "Akun {$user->name} berhasil di-approve.");
    }

    public function reject(User $user)
    {
        if ($user->status !== 'pending') {
            return back()->with('error', 'User sudah di-approve atau reject.');
        }

        $user->update(['status' => 'rejected']);

        return back()->with('success', "Akun {$user->name} ditolak.");
    }

    public function students(Request $request)
    {
        $query = User::students()->approved();

        if ($request->filled('class')) {
            $query->where('class', $request->class);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('nisn', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('name')->paginate(20);
        $classes = $this->getAvailableClasses();

        return view('admin.students', compact('students', 'classes'));
    }

    public function editStudent(User $user)
    {
        $classes = $this->getAvailableClasses();
        return view('admin.students-edit', compact('user', 'classes'));
    }

    // FIXED: Added phone normalization
    public function updateStudent(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'required|digits:10|unique:users,nisn,' . $user->id,
            'class' => 'required|string',
            'phone' => [
                'nullable',
                'string',
                'min:10',
                'max:15',
                'unique:users,phone,' . $user->id,
                'regex:/^(\+62|62|0)8[0-9]{8,13}$/'
            ],
            'email' => 'nullable|email|unique:users,email,' . $user->id,
        ]);

        // FIXED: Normalize phone number
        if (!empty($validated['phone'])) {
            $validated['phone'] = $this->normalizePhoneNumber($validated['phone']);
        }

        $user->update($validated);

        return redirect()->route('admin.students')->with('success', 'Data siswa berhasil diupdate.');
    }

    // FIXED: Added normalization method
    private function normalizePhoneNumber($phone)
    {
        // Remove all spaces and dashes
        $phone = preg_replace('/[\s\-]/', '', $phone);
        
        // Convert +628xxx to 08xxx
        if (strpos($phone, '+62') === 0) {
            return '0' . substr($phone, 3);
        }
        
        // Convert 628xxx to 08xxx
        if (strpos($phone, '62') === 0) {
            return '0' . substr($phone, 2);
        }
        
        // Already in 08xxx format
        return $phone;
    }

    public function deleteStudent(User $user)
    {
        if ($user->role !== 'student') {
            abort(403);
        }
        $name = $user->name;
        $user->delete();

        return back()->with('success', "Siswa {$name} berhasil dihapus.");
    }

    public function resetPassword(User $user)
    {
        if ($user->role !== 'student') {
            abort(403);
        }

        $newPassword = 'student' . rand(1000, 9999);
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "Password {$user->name} direset menjadi: {$newPassword}");
    }

    public function monitoring(Request $request)
    {
        $date = $request->get('date', today()->format('Y-m-d'));
        $class = $request->get('class');
        $status = $request->get('status');

        $students = User::students()->approved();

        if ($class) {
            $students->where('class', $class);
        }

        $students = $students->get();

        // Get attendances for the date
        $attendances = Attendance::whereDate('date', $date)
            ->with('user')
            ->get()
            ->keyBy('user_id');

        // Build monitoring data
        $monitoringData = $students->map(function($student) use ($attendances) {
            $attendance = $attendances->get($student->id);
            
            return [
                'student' => $student,
                'attendance' => $attendance,
                'status' => $attendance ? $attendance->status : 'alpha',
                'check_in' => $attendance ? $attendance->check_in : null,
                'check_out' => $attendance ? $attendance->check_out : null,
            ];
        });

        // Filter by status if provided
        if ($status) {
            $monitoringData = $monitoringData->filter(function($item) use ($status) {
                return $item['status'] === $status;
            });
        }

        $classes = $this->getAvailableClasses();

        return view('admin.monitoring', compact('monitoringData', 'date', 'classes'));
    }

    public function history(Request $request)
    {
        $query = Attendance::with('user');

        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        } elseif ($request->filled('date')) {
            $query->whereDate('date', $request->date);
        } else {
            $query->whereMonth('date', now()->month)
                  ->whereYear('date', now()->year);
        }

        if ($request->filled('class')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('class', $request->class);
            });
        }

        if ($request->filled('student_id')) {
            $query->where('user_id', $request->student_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $attendances = $query->orderBy('date', 'desc')
            ->orderBy('check_in', 'asc')
            ->paginate(50);

        $classes = $this->getAvailableClasses();
        $students = User::students()->approved()->orderBy('name')->get();

        return view('admin.history', compact('attendances', 'classes', 'students'));
    }

    public function reports(Request $request)
    {
        $month = $request->get('month', now()->month);
        $year = $request->get('year', now()->year);
        $class = $request->get('class');

        $query = User::students()->approved();

        if ($class) {
            $query->where('class', $class);
        }

        $students = $query->get();

        // Calculate attendance for each student
        $reportData = $students->map(function($student) use ($month, $year) {
            $attendances = Attendance::where('user_id', $student->id)
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            // FIXED: Better calculation (though still needs work day calculation)
            $totalDays = Carbon::createFromDate($year, $month, 1)->daysInMonth;
            $hadir = $attendances->where('status', 'hadir')->count();
            $terlambat = $attendances->where('status', 'terlambat')->count();
            
            // Note: This still counts weekends/holidays as alpha
            // For production, you should implement a school calendar
            $alpha = $totalDays - ($hadir + $terlambat);
            $percentage = $totalDays > 0 ? (($hadir + $terlambat) / $totalDays) * 100 : 0;

            return [
                'student' => $student,
                'hadir' => $hadir,
                'terlambat' => $terlambat,
                'alpha' => $alpha,
                'percentage' => round($percentage, 1),
            ];
        });

        $classes = $this->getAvailableClasses();

        return view('admin.reports', compact('reportData', 'month', 'year', 'classes'));
    }

    // FIXED: Export with proper filters
    public function exportExcel(Request $request)
    {
        $filters = [];
        
        // Handle month/year filter (for reports page)
        if ($request->filled('month') && $request->filled('year')) {
            $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($request->year, $request->month, 1)->endOfMonth();
            
            $filters['start_date'] = $startDate->format('Y-m-d');
            $filters['end_date'] = $endDate->format('Y-m-d');
        }
        // Handle date range filter (for history page)
        elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $filters['start_date'] = $request->start_date;
            $filters['end_date'] = $request->end_date;
        }
        
        // Handle class filter
        if ($request->filled('class')) {
            $filters['class'] = $request->class;
        }
        
        $filename = 'attendance_' . now()->format('Y-m-d_His') . '.xlsx';
        return Excel::download(new AttendanceExport($filters), $filename);
    }

    // FIXED: Export with proper filters
    public function exportPdf(Request $request)
    {
        $query = Attendance::with('user');

        // Handle month/year filter (for reports page)
        if ($request->filled('month') && $request->filled('year')) {
            $startDate = Carbon::createFromDate($request->year, $request->month, 1)->startOfMonth();
            $endDate = Carbon::createFromDate($request->year, $request->month, 1)->endOfMonth();
            
            $query->whereBetween('date', [$startDate, $endDate]);
        }
        // Handle date range filter (for history page)
        elseif ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('date', [$request->start_date, $request->end_date]);
        }

        if ($request->filled('class')) {
            $query->whereHas('user', function($q) use ($request) {
                $q->where('class', $request->class);
            });
        }

        $attendances = $query->orderBy('date', 'desc')->get();

        $pdf = Pdf::loadView('admin.export.attendance-pdf', compact('attendances'));
        return $pdf->download('attendance_' . now()->format('Y-m-d') . '.pdf');
    }

    public function settings()
    {
        $settings = Setting::all()->keyBy('key');
        return view('admin.settings', compact('settings'));
    }

    public function updateSettings(Request $request)
    {
        $validated = $request->validate([
            'check_in_time_limit' => 'required|date_format:H:i',
            'check_out_time_min' => 'required|date_format:H:i',
            'school_name' => 'required|string|max:255',
            'face_match_threshold' => 'required|numeric|min:0|max:1',
        ]);

        foreach ($validated as $key => $value) {
            Setting::set($key, $value);
        }

        return back()->with('success', 'Pengaturan berhasil diupdate.');
    }

    private function getAvailableClasses()
    {
        $tingkat = [10, 11, 12];
        $jurusan = ['DKV', 'SIJA', 'PB'];
        $rombel = [1, 2, 3];

        $classes = [];
        foreach ($tingkat as $t) {
            foreach ($jurusan as $j) {
                foreach ($rombel as $r) {
                    $classes[] = "$t $j $r";
                }
            }
        }
        return $classes;
    }
}