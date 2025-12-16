namespace App\Services;

class FaceRecognitionService
{
    /**
     * Compare two face descriptors
     * Returns distance (0 = identical, 1 = completely different)
     */
    public static function compareFaces($descriptor1, $descriptor2)
    {
        if (!is_array($descriptor1) || !is_array($descriptor2)) {
            return 1; // Max distance if invalid
        }

        if (count($descriptor1) !== count($descriptor2)) {
            return 1;
        }

        // Euclidean distance
        $sum = 0;
        for ($i = 0; $i < count($descriptor1); $i++) {
            $diff = $descriptor1[$i] - $descriptor2[$i];
            $sum += $diff * $diff;
        }

        return sqrt($sum);
    }

    /**
     * Check if two faces match based on threshold
     */
    public static function isMatch($descriptor1, $descriptor2, $threshold = 0.6)
    {
        $distance = self::compareFaces($descriptor1, $descriptor2);
        return $distance < $threshold;
    }

    /**
     * Find best matching user from face descriptor
     */
    public static function findMatchingUser($inputDescriptor)
    {
        $threshold = \App\Models\Setting::get('face_match_threshold', 0.6);
        $users = \App\Models\User::students()
            ->approved()
            ->whereNotNull('face_descriptor')
            ->get();

        $bestMatch = null;
        $bestDistance = PHP_FLOAT_MAX;

        foreach ($users as $user) {
            $storedDescriptor = json_decode($user->face_descriptor, true);
            
            if (!$storedDescriptor) continue;

            $distance = self::compareFaces($inputDescriptor, $storedDescriptor);

            if ($distance < $threshold && $distance < $bestDistance) {
                $bestDistance = $distance;
                $bestMatch = $user;
            }
        }

        return [
            'user' => $bestMatch,
            'distance' => $bestDistance,
            'confidence' => $bestMatch ? (1 - $bestDistance) * 100 : 0,
        ];
    }
}