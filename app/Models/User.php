<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
    ];

    // FIXED: Added face_descriptor casting for better performance
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'face_descriptor' => 'array', // Auto decode/encode JSON
    ];

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

    // FIXED: Simplified with casting
    public function getFaceDescriptor()
    {
        // face_descriptor is already an array due to casting
        return $this->face_descriptor;
    }

    public function setFaceDescriptor(array $descriptor)
    {
        // Will be automatically JSON encoded due to casting
        $this->face_descriptor = $descriptor;
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