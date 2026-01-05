<?php

namespace App\Imports;

use App\Models\User;
use App\Helpers\PhoneHelper;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsOnError;
use Maatwebsite\Excel\Concerns\SkipsErrors;
use Illuminate\Support\Str;

class StudentsImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnError
{
    use SkipsErrors;
    
    protected $results = [];

    public function model(array $row)
    {
        // Generate username dari nama
        $username = $this->generateUsername($row['nama_lengkap']);
        
        // Generate password random
        $password = 'student' . rand(1000, 9999);
        
        // Normalize phone
        $phone = isset($row['no_hp']) ? PhoneHelper::normalize($row['no_hp']) : null;
        
        try {
            // Validasi NISN format
            if (!preg_match('/^\d{10}$/', $row['nisn_10_digit'])) {
                throw new \Exception('NISN harus 10 digit angka');
            }
            
            $user = User::create([
                'nisn' => $row['nisn_10_digit'],
                'name' => $row['nama_lengkap'],
                'username' => $username,
                'password' => Hash::make($password),
                'class' => $row['kelas_contoh_10_dkv_1'],
                'phone' => $phone,
                'email' => $row['email_opsional'] ?? null,
                'role' => 'student',
                'status' => 'pending',
            ]);
            
            $this->results[] = [
                'nisn' => $row['nisn_10_digit'],
                'nama' => $row['nama_lengkap'],
                'kelas' => $row['kelas_contoh_10_dkv_1'],
                'username' => $username,
                'password' => $password,
                'status' => 'Berhasil',
            ];
            
            return $user;
            
        } catch (\Exception $e) {
            $this->results[] = [
                'nisn' => $row['nisn_10_digit'] ?? '-',
                'nama' => $row['nama_lengkap'] ?? '-',
                'kelas' => $row['kelas_contoh_10_dkv_1'] ?? '-',
                'username' => '-',
                'password' => '-',
                'status' => 'Gagal: ' . $e->getMessage(),
            ];
            
            return null;
        }
    }

    public function rules(): array
    {
        return [
            'nisn_10_digit' => [
                'required',
                'digits:10',
                function($attribute, $value, $fail) {
                    if (User::where('nisn', $value)->exists()) {
                        $fail('NISN sudah terdaftar');
                    }
                }
            ],
            'nama_lengkap' => 'required|string|max:255',
            'kelas_contoh_10_dkv_1' => 'required|string',
            'no_hp_opsional' => 'nullable|string',
            'email_opsional' => [
                'nullable',
                'email',
                function($attribute, $value, $fail) {
                    if ($value && User::where('email', $value)->exists()) {
                        $fail('Email sudah terdaftar');
                    }
                }
            ],
        ];
    }

    public function customValidationMessages()
    {
        return [
            'nisn_10_digit.required' => 'NISN wajib diisi',
            'nisn_10_digit.digits' => 'NISN harus 10 digit',
            'nama_lengkap.required' => 'Nama wajib diisi',
            'kelas_contoh_10_dkv_1.required' => 'Kelas wajib diisi',
        ];
    }

    private function generateUsername($name)
    {
        $username = strtolower(Str::slug(explode(' ', $name)[0], ''));
        
        $counter = 1;
        $originalUsername = $username;
        
        while (User::where('username', $username)->exists()) {
            $username = $originalUsername . $counter;
            $counter++;
        }
        
        return $username;
    }

    public function getResults()
    {
        return $this->results;
    }
}