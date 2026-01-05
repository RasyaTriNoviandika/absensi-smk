<?php

namespace App\Imports;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Illuminate\Support\Str;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation
{
    protected $results = [];

    public function model(array $row)
    {
        // Generate username otomatis dari nama
        $username = $this->generateUsername($row['nama']);
        
        // Generate password random
        $password = 'siswa' . rand(1000, 9999);
        
        try {
            $user = User::create([
                'nisn' => $row['nisn'],
                'name' => $row['nama'],
                'username' => $username,
                'password' => Hash::make($password),
                'class' => $row['kelas'],
                'phone' => $row['no_hp'] ?? null,
                'email' => $row['email'] ?? null,
                'role' => 'student',
                'status' => 'pending', // Admin harus approve
            ]);
            
            // Simpan hasil untuk export
            $this->results[] = [
                'nisn' => $row['nisn'],
                'nama' => $row['nama'],
                'kelas' => $row['kelas'],
                'username' => $username,
                'password' => $password,
                'status' => 'Berhasil',
            ];
            
            return $user;
            
        } catch (\Exception $e) {
            $this->results[] = [
                'nisn' => $row['nisn'],
                'nama' => $row['nama'],
                'kelas' => $row['kelas'],
                'username' => $username,
                'password' => '-',
                'status' => 'Gagal: ' . $e->getMessage(),
            ];
            
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nisn' => 'required|digits:10|unique:users,nisn',
            'nama' => 'required|string|max:255',
            'kelas' => 'required|string',
            'no_hp' => 'nullable|string',
            'email' => 'nullable|email|unique:users,email',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nisn.required' => 'NISN wajib diisi',
            'nisn.digits' => 'NISN harus 10 digit',
            'nisn.unique' => 'NISN sudah terdaftar',
            'nama.required' => 'Nama wajib diisi',
            'kelas.required' => 'Kelas wajib diisi',
        ];
    }

    private function generateUsername($name)
    {
        // Ambil nama depan, lowercase, hapus spasi
        $username = strtolower(str_replace(' ', '', explode(' ', $name)[0]));
        
        // Cek apakah username sudah ada
        $exists = User::where('username', $username)->exists();
        
        if ($exists) {
            // Tambahkan angka random
            $username .= rand(100, 999);
        }
        
        return $username;
    }

    public function getResults()
    {
        return $this->results;
    }
}