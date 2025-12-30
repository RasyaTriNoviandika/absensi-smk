<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'date',
        'check_in',
        'check_in_status',
        'check_in_photo',
        'check_out',
        'check_out_photo',
        'early_checkout_photo', 
        'status',
        'notes',
    ];

    protected $casts = [
        'date' => 'date',
        'check_in' => 'datetime:H:i',
        'check_out' => 'datetime:H:i',
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Scopes
    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('date', now()->month)
            ->whereYear('date', now()->year);
    }

    public function scopeByDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeDateRange($query, $start, $end)
    {
        return $query->whereBetween('date', [$start, $end]);
    }

    // Helper Methods
    public function isLate()
    {
        return $this->check_in_status === 'terlambat';
    }

    public function isPresent()
    {
        return in_array($this->status, ['hadir', 'terlambat']);
    }

    public function isAlpha()
    {
        return $this->status === 'alpha';
    }

    public function getFormattedCheckIn()
    {
        return $this->check_in ? Carbon::parse($this->check_in)->format('H:i') : '-';
    }

    public function getFormattedCheckOut()
    {
        return $this->check_out ? Carbon::parse($this->check_out)->format('H:i') : '-';
    }

    public function getStatusBadge()
    {
        return match($this->status) {
            'hadir' => '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Hadir</span>',
            'terlambat' => '<span class="px-2 py-1 text-xs rounded-full bg-yellow-100 text-yellow-800">Terlambat</span>',
            'alpha' => '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Alpha</span>',
            default => '<span class="px-2 py-1 text-xs rounded-full bg-gray-100 text-gray-800">-</span>',
        };
    }
}