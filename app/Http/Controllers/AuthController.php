<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Helpers\PhoneHelper; 

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
        //  FIXED: Use PhoneHelper for validation
        $validated = $request->validate([
            'nisn' => 'required|digits:10|unique:users,nisn',
            'name' => 'required|string|max:255',
            'username' => [
                'required',
                'string',
                'max:255',
                'unique:users,username',
                'alpha_dash'
            ],
            'password' => 'required|string|min:8|confirmed',
            'class' => 'required|string',
            'phone' => PhoneHelper::validationRule(), // ✅ FIXED
            'email' => 'nullable|email|unique:users,email',
            'face_descriptor' => 'required|json',
        ], [
            'username.unique' => 'Username sudah digunakan. Pilih username lain.',
            'username.alpha_dash' => 'Username hanya boleh huruf, angka, dash (-), dan underscore (_).',
            'nisn.unique' => 'NISN sudah terdaftar',
            'email.unique' => 'Email sudah terdaftar',
        ]);

        // ✅ FIXED: Normalize phone using helper
        $normalizedPhone = PhoneHelper::normalize($validated['phone'] ?? null);

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