<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;


Route::get('/', function () {
    return view('welcome');
})->name('home');

// Auth Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout 
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Student routes
Route::middleware(['auth', 'student'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'student'])->name('student.dashboard');
    
    // maksimal 10 request per menit untuk check-in/check-out
    Route::middleware('throttle:10,1')->group(function () {
        Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
        Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    });
    
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
});

// Admin routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    
    // Approval management
    Route::get('/approvals', [AdminController::class, 'approvals'])->name('approvals');
    Route::post('/approvals/{user}/approve', [AdminController::class, 'approve'])->name('approve');
    Route::post('/approvals/{user}/reject', [AdminController::class, 'reject'])->name('reject');
    
    // Student management
    Route::get('/students', [AdminController::class, 'students'])->name('students');
    Route::get('/students/{user}/edit', [AdminController::class, 'editStudent'])->name('students.edit');
    Route::put('/students/{user}', [AdminController::class, 'updateStudent'])->name('students.update');
    Route::delete('/students/{user}', [AdminController::class, 'deleteStudent'])->name('students.delete');
    Route::post('/students/{user}/reset-password', [AdminController::class, 'resetPassword'])->name('students.reset-password');
    
    // Monitoring & reporting
    Route::get('/monitoring', [AdminController::class, 'monitoring'])->name('monitoring');
    Route::get('/history', [AdminController::class, 'history'])->name('history');
    Route::get('/reports', [AdminController::class, 'reports'])->name('reports');
    
    // Export
    Route::get('/export/excel', [AdminController::class, 'exportExcel'])->name('export.excel');
    Route::get('/export/pdf', [AdminController::class, 'exportPdf'])->name('export.pdf');
    
    // Settings
    Route::get('/settings', [AdminController::class, 'settings'])->name('settings');
    Route::put('/settings', [AdminController::class, 'updateSettings'])->name('settings.update');
});