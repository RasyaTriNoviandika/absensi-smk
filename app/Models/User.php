<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    // ✅ FIXED: Hapus face_descriptor dari guarded
    protected $guarded = [
        'id',
        'role',
        'status',
        // 'face_descriptor',  ❌ DIHAPUS!
        // 'face_descriptor_hash',  ❌ DIHAPUS!
    ];

    // ✅ ATAU gunakan $fillable (lebih aman)
    protected $fillable = [
        'nisn',
        'username',
        'name',
        'email',
        'password',
        'class',
        'phone',
        'address',
        'face_descriptor',  // ✅ DITAMBAHKAN
        'face_descriptor_hash',
        'profile_photo',
        'last_login_at',
        'last_login_ip',
        'face_registered_at',
        'qr_generated_at',
        'qr_token',
        'qr_token_used_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'face_descriptor',
        'face_descriptor_hash',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'last_login_at' => 'datetime',
        'face_registered_at' => 'datetime',
        'qr_generated_at' => 'datetime',
    ];

    // ✅ FIXED: Setter untuk face_descriptor
    public function setFaceDescriptorAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['face_descriptor'] = null;
            $this->attributes['face_descriptor_hash'] = null;
            return;
        }
        
        // Jika sudah string JSON, simpan langsung
        if (is_string($value)) {
            $value = json_decode($value, true);
        }
        
        // Harus array dengan 128 elemen
        if (!is_array($value) || count($value) !== 128) {
            Log::error('Invalid face descriptor', [
                'user_id' => $this->id ?? 'new',
                'type' => gettype($value),
                'count' => is_array($value) ? count($value) : 0
            ]);
            throw new \Exception('Face descriptor must be array of 128 numbers');
        }
        
        try {
            $jsonValue = json_encode($value);
            
            // Encrypt dengan Laravel Crypt (AES-256-CBC)
            $encrypted = Crypt::encryptString($jsonValue);
            
            // Generate HMAC untuk integrity verification
            $hash = hash_hmac('sha256', $jsonValue, config('app.key'));
            
            $this->attributes['face_descriptor'] = $encrypted;
            $this->attributes['face_descriptor_hash'] = $hash;
            $this->attributes['face_registered_at'] = now();
            
            Log::info('Face descriptor saved', [
                'user_id' => $this->id ?? 'new',
                'username' => $this->username ?? 'unknown'
            ]);
            
        } catch (\Exception $e) {
            Log::error('Face descriptor encryption failed', [
                'error' => $e->getMessage(),
                'user_id' => $this->id ?? 'new',
            ]);
            throw new \Exception('Failed to secure face descriptor: ' . $e->getMessage());
        }
    }

    // ✅ FIXED: Getter untuk face_descriptor
    public function getFaceDescriptorAttribute($value)
    {
        if (empty($value)) {
            return null;
        }
        
        try {
            // Decrypt
            $decrypted = Crypt::decryptString($value);
            
            // Verify integrity (jika hash field ada)
            if (!empty($this->attributes['face_descriptor_hash'])) {
                $expectedHash = hash_hmac('sha256', $decrypted, config('app.key'));
                
                if (!hash_equals($this->attributes['face_descriptor_hash'], $expectedHash)) {
                    Log::critical('SECURITY: Face descriptor tampered', [
                        'user_id' => $this->id,
                        'timestamp' => now(),
                    ]);

                    // Manual review
                    $this->update(['status' => 'pending', 'notes' => 'Security Check Failed']);
                    
                    return null;
                }
            }
            
            return json_decode($decrypted, true);
            
        } catch (\Exception $e) {
            Log::warning('Face descriptor decryption failed', [
                'user_id' => $this->id,
                'error' => $e->getMessage(),
            ]);
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

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
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

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function getFaceDescriptor()
    {
        return $this->face_descriptor;
    }

    public function setFaceDescriptor(array $descriptor)
    {
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
    
    // SECURITY: Track login attempts
    public function recordLoginAttempt($success, $ipAddress)
    {
        Log::info('Login attempt', [
            'user_id' => $this->id,
            'success' => $success,
            'ip' => $ipAddress,
            'timestamp' => now(),
        ]);
        
        if ($success) {
            $this->update([
                'last_login_at' => now(),
                'last_login_ip' => $ipAddress,
            ]);
        }
    }
}