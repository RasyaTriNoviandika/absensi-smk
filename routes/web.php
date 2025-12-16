<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\AdminController;

// Root route - redirect based on auth status
Route::get('/', function () {
    if (auth()->check()) {
        if (auth()->user()->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        return redirect()->route('student.dashboard');
    }
    return redirect()->route('login');
})->name('home');

// Auth routes - only for guests
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

// Logout - only for authenticated users
Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Student routes
Route::middleware(['auth'])->group(function () {
    // Check if user is student and approved
    Route::get('/dashboard', function() {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        if (!$user->isApproved()) {
            auth()->logout();
            return redirect()->route('login')->with('error', 'Akun Anda masih menunggu approval dari admin.');
        }
        
        return app(DashboardController::class)->student();
    })->name('student.dashboard');
    
    Route::post('/attendance/check-in', [AttendanceController::class, 'checkIn'])->name('attendance.checkin');
    Route::post('/attendance/check-out', [AttendanceController::class, 'checkOut'])->name('attendance.checkout');
    Route::get('/attendance/history', [AttendanceController::class, 'history'])->name('attendance.history');
});

// Admin routes
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    // Middleware check admin inline
    Route::get('/dashboard', function() {
        if (!auth()->user()->isAdmin()) {
            abort(403, 'Unauthorized');
        }
        return app(AdminController::class)->dashboard();
    })->name('dashboard');
    
    Route::get('/approvals', function() {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->approvals();
    })->name('approvals');
    
    Route::post('/approvals/{user}/approve', function($user) {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->approve(\App\Models\User::findOrFail($user));
    })->name('approve');
    
    Route::post('/approvals/{user}/reject', function($user) {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->reject(\App\Models\User::findOrFail($user));
    })->name('reject');
    
    Route::get('/students', function() {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->students();
    })->name('students');
    
    Route::get('/students/{user}/edit', function($user) {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->editStudent(\App\Models\User::findOrFail($user));
    })->name('students.edit');
    
    Route::put('/students/{user}', function($user) {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->updateStudent(request(), \App\Models\User::findOrFail($user));
    })->name('students.update');
    
    Route::delete('/students/{user}', function($user) {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->deleteStudent(\App\Models\User::findOrFail($user));
    })->name('students.delete');
    
    Route::post('/students/{user}/reset-password', function($user) {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->resetPassword(\App\Models\User::findOrFail($user));
    })->name('students.reset-password');
    
    Route::get('/monitoring', function() {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->monitoring();
    })->name('monitoring');
    
    Route::get('/history', function() {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->history();
    })->name('history');
    
    Route::get('/reports', function() {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->reports();
    })->name('reports');
    
    Route::get('/export/excel', function() {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->exportExcel();
    })->name('export.excel');
    
    Route::get('/export/pdf', function() {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->exportPdf();
    })->name('export.pdf');
    
    Route::get('/settings', function() {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->settings();
    })->name('settings');
    
    Route::put('/settings', function() {
        if (!auth()->user()->isAdmin()) abort(403);
        return app(AdminController::class)->updateSettings(request());
    })->name('settings.update');
});