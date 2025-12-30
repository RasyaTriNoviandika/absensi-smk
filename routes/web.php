<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;

// Livewire Components
use App\Http\Livewire\Admin\Dashboard as AdminDashboard;
use App\Http\Livewire\Admin\Approvals as AdminApprovals;
use App\Http\Livewire\Admin\Monitoring as AdminMonitoring;
use App\Http\Livewire\Admin\History as AdminHistory;
use App\Http\Livewire\Admin\Reports as AdminReports;
use App\Http\Livewire\Student\Dashboard as StudentDashboard;
use App\Http\Livewire\Student\AttendanceHistory as StudentAttendanceHistory;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', fn() => view('welcome'))->name('home');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes - Livewire
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['auth', 'admin'])->group(function () {
    // Livewire Pages
    Route::get('/dashboard', AdminDashboard::class)->name('dashboard');
    Route::get('/approvals', AdminApprovals::class)->name('approvals');
    Route::get('/monitoring', AdminMonitoring::class)->name('monitoring');
    Route::get('/history', AdminHistory::class)->name('history');
    Route::get('/reports', AdminReports::class)->name('reports');
    
    // Livewire Students Page
    Route::get('/students', \App\Http\Livewire\Admin\Students::class)->name('students');
    
    // Traditional Controller Routes (untuk non-Livewire actions)
    Route::get('/students/{user}/edit', [AdminController::class, 'editStudent'])->name('students.edit');
    Route::put('/students/{user}', [AdminController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{user}', [AdminController::class, 'deleteStudent'])->name('students.delete');
    Route::post('/students/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('students.reset-password');
    
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
    
    // Export Routes
    Route::get('/export/excel', [AdminController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [AdminController::class, 'exportPdf'])->name('export.pdf');
});

/*
|--------------------------------------------------------------------------
| Student Routes - Livewire
|--------------------------------------------------------------------------
*/
Route::prefix('student')->name('student.')->middleware(['auth', 'student'])->group(function () {
    // Livewire Pages
    Route::get('/dashboard', StudentDashboard::class)->name('dashboard');
    Route::get('/history', StudentAttendanceHistory::class)->name('history');
});

/*
|--------------------------------------------------------------------------
| Attendance API Routes (AJAX)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'student'])->prefix('attendance')->name('attendance.')->group(function () {
    Route::post('/checkin', [AttendanceController::class, 'checkIn'])->name('checkin');
    Route::post('/checkout', [AttendanceController::class, 'checkOut'])->name('checkout');
});