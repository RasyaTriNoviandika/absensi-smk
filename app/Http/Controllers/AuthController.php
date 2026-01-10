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
    $request->validate([
        'username' => 'required',
        'password' => 'required',
    ]);

    if (!Auth::attempt($request->only('username', 'password'), $request->filled('remember'))) {
        return back()->with('error', 'Username atau password salah.')
                     ->withInput();
    }

    $user = auth()->user();

    if ($user->isStudent()) {
        if ($user->status === 'pending') {
            Auth::logout();
            return back()->with('warning', 'Akun Anda masih menunggu approval admin.');
        }

        if ($user->status === 'rejected') {
            Auth::logout();
            return back()->with('error', 'Pendaftaran Anda ditolak.');
        }
    }

    return $this->redirectBasedOnRole();
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
        $validated = $request->validate([
            'nisn' => 'required|digits:10|unique:users,nisn',
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|alpha_dash|unique:users,username',
            'password' => 'required|string|min:8|confirmed',
            'class' => 'required|string',
            'phone' => PhoneHelper::validationRule(),
            'email' => 'nullable|email|unique:users,email',
            'face_descriptor' => 'required|json',
        ]);

        $normalizedPhone = PhoneHelper::normalize($validated['phone']);

        User::create([
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
        ]);

        return redirect()->route('login')
            ->with('success', 'Registrasi berhasil! Tunggu approval admin untuk dapat login.');
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
            ->with('warning', 'Akun Anda masih menunggu approval admin.');
    }
}
