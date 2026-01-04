<?php

namespace App\Helpers;

class PhoneHelper
{
    /**
     * Normalize phone number to 08xxx format
     * 
     * @param string|null $phone
     * @return string|null
     */
    public static function normalize($phone)
    {
        if (empty($phone)) {
            return null;
        }
        
        // Remove all spaces, dashes, and parentheses
        $phone = preg_replace('/[\s\-\(\)]/', '', $phone);
        
        // Convert +628xxx to 08xxx
        if (strpos($phone, '+62') === 0) {
            return '0' . substr($phone, 3);
        }
        
        // Convert 628xxx to 08xxx
        if (strpos($phone, '62') === 0 && strlen($phone) >= 11) {
            return '0' . substr($phone, 2);
        }
        
        // Already in 08xxx format or invalid
        return $phone;
    }

    /**
     * Validate phone number format
     * 
     * @param string|null $phone
     * @return bool
     */
    public static function isValid($phone)
    {
        if (empty($phone)) {
            return true; // Null is valid (optional field)
        }
        
        $normalized = self::normalize($phone);
        
        // Must start with 08 and be 10-13 digits total
        return preg_match('/^08[0-9]{8,11}$/', $normalized);
    }

    /**
     * Get validation rule for phone field
     * 
     * @param int|null $exceptUserId - For update validation
     * @return array
     */
    public static function validationRule($exceptUserId = null)
    {
        $rules = [
            'nullable',
            'string',
            'min:10',
            'max:15',
        ];

        // ✅ FIXED: Unique validation setelah normalisasi
        if ($exceptUserId) {
            $rules[] = function ($attribute, $value, $fail) use ($exceptUserId) {
                if (empty($value)) return;
                
                $normalized = PhoneHelper::normalize($value);
                
                $exists = \App\Models\User::where('phone', $normalized)
                    ->where('id', '!=', $exceptUserId)
                    ->exists();
                
                if ($exists) {
                    $fail('Nomor HP sudah terdaftar.');
                }
            };
        } else {
            $rules[] = function ($attribute, $value, $fail) {
                if (empty($value)) return;
                
                $normalized = PhoneHelper::normalize($value);
                
                $exists = \App\Models\User::where('phone', $normalized)->exists();
                
                if ($exists) {
                    $fail('Nomor HP sudah terdaftar.');
                }
            };
        }

        return $rules;
    }
}