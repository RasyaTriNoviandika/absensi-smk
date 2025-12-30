<?php

use Illuminate\Support\Facades\Route;

// Admin Livewire Components
use App\Http\Livewire\Admin\Dashboard as AdminDashboard;
use App\Http\Livewire\Admin\Approvals as AdminApprovals;
use App\Http\Livewire\Admin\Monitoring as AdminMonitoring;
use App\Http\Livewire\Admin\History as AdminHistory;
use App\Http\Livewire\Admin\Reports as AdminReports;
use App\Http\Livewire\Admin\Students as AdminStudents;
use App\Http\Livewire\Admin\Settings as AdminSettings;

// Student Livewire Components
use App\Http\Livewire\Student\Dashboard as StudentDashboard;
use App\Http\Livewire\Student\AttendanceHistory as StudentAttendanceHistory;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Admin Routes
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('approvals', AdminApprovals::class)->name('approvals');
    Route::get('monitoring', AdminMonitoring::class)->name('monitoring');
    Route::get('history', AdminHistory::class)->name('history');
    Route::get('reports', AdminReports::class)->name('reports');
    Route::get('students', AdminStudents::class)->name('students');
    Route::get('settings', AdminSettings::class)->name('settings');
});

// Student Routes
Route::prefix('student')->name('student.')->group(function () {
    Route::get('dashboard', StudentDashboard::class)->name('dashboard');
    Route::get('attendance-history', StudentAttendanceHistory::class)->name('attendance-history');
});
