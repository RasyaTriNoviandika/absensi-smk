<?php

namespace App\Services;

class FaceRecognitionService
{
    public static function compareFaces($descriptor1, $descriptor2)
    {
        if (!is_array($descriptor1) || !is_array($descriptor2)) {
            return 1;
        }

        if (count($descriptor1) !== count($descriptor2)) {
            return 1;
        }

        $sum = 0;
        $count = count($descriptor1);
        
        for ($i = 0; $i < $count; $i++) {
            $diff = $descriptor1[$i] - $descriptor2[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    public static function isMatch($descriptor1, $descriptor2, $threshold = 0.6)
    {
        $distance = self::compareFaces($descriptor1, $descriptor2);
        return $distance < $threshold;
    }
}