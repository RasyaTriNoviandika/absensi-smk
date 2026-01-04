<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Crypt;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'nisn',
        'username',
        'name',
        'email',
        'password',
        'role',
        'class',
        'phone',
        'address',
        'status',
        'face_descriptor',
        'profile_photo',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'face_descriptor', // ✅ FIXED: Hide dari JSON response
    ];

    // ✅ FIXED: Casting dengan encryption
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        // ✅ REMOVED: 'face_descriptor' => 'array', 
        // Pakai encrypted casting manual di accessor/mutator
    ];

    // ✅ FIXED: Encrypt saat set face_descriptor
    public function setFaceDescriptorAttribute($value)
    {
        if (is_array($value)) {
            $value = json_encode($value);
        }
        
        // Encrypt before saving to database
        $this->attributes['face_descriptor'] = Crypt::encryptString($value);
    }

    // ✅ FIXED: Decrypt saat get face_descriptor
    public function getFaceDescriptorAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        try {
            // Decrypt from database
            $decrypted = Crypt::decryptString($value);
            return json_decode($decrypted, true);
        } catch (\Exception $e) {
            // Jika gagal decrypt (data lama), return null
            \Log::warning('Failed to decrypt face_descriptor for user ' . $this->id);
            return null;
        }
    }

    // Relationships
    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    // Scopes
    public function scopeStudents($query)
    {
        return $query->where('role', 'student');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeByClass($query, $class)
    {
        return $query->where('class', $class);
    }

    // Helper Methods
    public function isAdmin()
    {
        return $this->role === 'admin';
    }

    public function isStudent()
    {
        return $this->role === 'student';
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    // ✅ FIXED: Simplified accessor (sudah di-handle di getFaceDescriptorAttribute)
    public function getFaceDescriptor()
    {
        return $this->face_descriptor;
    }

    // ✅ FIXED: Simplified mutator
    public function setFaceDescriptor(array $descriptor)
    {
        $this->face_descriptor = $descriptor; // Will auto-encrypt via mutator
        $this->save();
    }

    // Get today's attendance
    public function todayAttendance()
    {
        return $this->attendances()
            ->whereDate('date', today())
            ->first();
    }

    // Check if already checked in today
    public function hasCheckedInToday()
    {
        return $this->attendances()
            ->whereDate('date', today())
            ->whereNotNull('check_in')
            ->exists();
    }

    // Check if already checked out today
    public function hasCheckedOutToday()
    {
        return $this->attendances()
            ->whereDate('date', today())
            ->whereNotNull('check_out')
            ->exists();
    }
}