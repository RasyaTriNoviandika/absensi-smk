<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class AuthController extends Controller
{
    public function showLogin()
    {
        // Jika sudah login, redirect ke dashboard sesuai role
        if (auth()->check()) {
            return $this->redirectBasedOnRole();
        }
        
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => 'required',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            // Check if user is approved (for students)
            $user = auth()->user();
            if ($user->isStudent() && !$user->isApproved()) {
                Auth::logout();
                return back()->withErrors([
                    'username' => 'Akun Anda masih menunggu approval dari admin.',
                ]);
            }
            
            return $this->redirectBasedOnRole();
        }

        return back()->withErrors([
            'username' => 'Username atau password salah.',
        ])->onlyInput('username');
    }

    public function showRegister()
    {
        // Jika sudah login, redirect
        if (auth()->check()) {
            return $this->redirectBasedOnRole();
        }
        
        return view('auth.register');
    }

    public function register(Request $request)
{
    $validated = $request->validate([
        'nisn' => 'required|digits:10|unique:users,nisn',
        'name' => 'required|string|max:255',
        'username' => 'required|string|max:255|unique:users,username',
        'password' => 'required|string|min:8|confirmed',
        'class' => 'required|string',
        // PERBAIKAN: Phone validation dengan format Indonesia
        // Format yang diterima:
        // - 08xxxxxxxxxx (10-13 digit, minimal 08xxxxxxxxx)
        // - +628xxxxxxxxx (11-14 digit setelah +62)
        // - 628xxxxxxxxx (10-13 digit)
        'phone' => [
            'nullable',
            'string',
            'min:10',
            'max:15',
            'unique:users,phone',
            'regex:/^(\+62|62|0)8[0-9]{8,13}$/'
        ],
        'email' => 'nullable|email|unique:users,email',
        'face_descriptor' => 'required|json',
    ], [
        // Custom error messages
        'phone.regex' => 'Format nomor HP tidak valid. Gunakan format: 08xxxxxxxxxx, 628xxxxxxxxxx, atau +628xxxxxxxxxx',
        'phone.min' => 'Nomor HP minimal 10 digit',
        'phone.max' => 'Nomor HP maksimal 15 digit',
        'phone.unique' => 'Nomor HP sudah terdaftar',
    ]);

    // Normalisasi phone number ke format 08xxx
    $normalizedPhone = null;
    if (!empty($validated['phone'])) {
        $normalizedPhone = $this->normalizePhoneNumber($validated['phone']);
    }

    $user = User::create([
        'nisn' => $validated['nisn'],
        'name' => $validated['name'],
        'username' => $validated['username'],
        'password' => Hash::make($validated['password']),
        'class' => $validated['class'],
        'phone' => $normalizedPhone,
        'email' => $validated['email'] ?? null,
        'role' => 'student',
        'status' => 'pending',
        'face_descriptor' => $validated['face_descriptor'],
        'profile_photo' => null,
    ]);

    return redirect()->route('login')
        ->with('success', 'Registrasi berhasil! Tunggu approval dari admin untuk dapat login.');
}

/**
 * Normalize phone number to 08xxx format
 * Converts +628xxx or 628xxx to 08xxx
 */
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

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login')
            ->with('success', 'Anda telah logout.');
    }

    private function redirectBasedOnRole()
    {
        $user = auth()->user();
        
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard');
        }
        
        if ($user->isStudent() && $user->isApproved()) {
            return redirect()->route('student.dashboard');
        }
        
        // Jika student belum approved, logout
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Akun Anda masih menunggu approval dari admin.');
    }
}