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
        if (auth()->check()) {
            return $this->redirectBasedOnRole();
        }
        
        return view('auth.register');
    }

    public function register(Request $request)
    {
        //  SECURITY FIX: Validasi dengan unique constraint
        $validated = $request->validate([
            'nisn' => 'required|digits:10|unique:users,nisn',
            'name' => 'required|string|max:255',
            // TAMBAHAN: Username harus unique
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
                'alpha_dash' // Hanya huruf, angka, dash, underscore
            ],
            'password' => 'required|string|min:8|confirmed',
            'class' => 'required|string',
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
            'username.unique' => 'Username sudah digunakan. Pilih username lain.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash (-), dan underscore (_).',
            'phone.regex' => 'Format nomor HP tidak valid. Gunakan format: 08xxx, 628xxx, atau +628xxx',
            'phone.min' => 'Nomor HP minimal 10 digit',
            'phone.max' => 'Nomor HP maksimal 15 digit',
            'phone.unique' => 'Nomor HP sudah terdaftar',
            'nisn.unique' => 'NISN sudah terdaftar',
            'email.unique' => 'Email sudah terdaftar',
        ]);

        // Normalisasi phone number
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

    private function normalizePhoneNumber($phone)
    {
        $phone = preg_replace('/[\s\-]/', '', $phone);
        
        if (strpos($phone, '+62') === 0) {
            return '0' . substr($phone, 3);
        }
        
        if (strpos($phone, '62') === 0) {
            return '0' . substr($phone, 2);
        }
        
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
        
        Auth::logout();
        return redirect()->route('login')
            ->with('error', 'Akun Anda masih menunggu approval dari admin.');
    }
}