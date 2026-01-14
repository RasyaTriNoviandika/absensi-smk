<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        //  VALIDASI dengan face_descriptor
        $validator = Validator::make($request->all(), [
            'nisn' => 'required|string|size:10|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'name' => 'required|string|max:255',
            'class' => 'required|string',
            'phone' => 'nullable|regex:/^(\+62|62|0)8[0-9]{8,13}$/',
            'email' => 'nullable|email|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'face_descriptor' => 'required|string', 
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            //  DECODE face_descriptor dari JSON string
            $faceDescriptor = json_decode($request->face_descriptor, true);
            
         
            if (!is_array($faceDescriptor) || count($faceDescriptor) !== 128) {
                return redirect()->back()
                    ->withErrors(['face_descriptor' => 'Data wajah tidak valid. Silakan capture ulang.'])
                    ->withInput();
            }

            //  CREATE USER dengan face_descriptor
            $user = User::create([
                'nisn' => $request->nisn,
                'username' => $request->username,
                'name' => $request->name,
                'class' => $request->class,
                'phone' => $request->phone,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'role' => 'student',
                'status' => 'pending', // Admin perlu approve
                'face_descriptor' => json_encode($faceDescriptor), // ✅ SIMPAN sebagai JSON string
            ]);

            Log::info('User registered successfully', [
                'user_id' => $user->id,
                'username' => $user->username,
                'face_descriptor_count' => count($faceDescriptor)
            ]);

            //  REDIRECT ke halaman sukses
            return redirect()->route('login')
                ->with('success', 'Registrasi berhasil! Tunggu persetujuan admin untuk bisa login.');

        } catch (\Exception $e) {
            Log::error('Registration failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            return redirect()->back()
                ->withErrors(['error' => 'Terjadi kesalahan saat registrasi. Silakan coba lagi.'])
                ->withInput();
        }
    }
}