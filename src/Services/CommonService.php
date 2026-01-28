<?php

namespace App\Services;

use DateTime;
use DateTimeZone;

class CommonService{
    public function myDate(?string $date = null, $tzName  = 'Asia/Kolkata'): \DateTimeImmutable
    {
        $tz = new \DateTimeZone($tzName);

        if ($date === null) {
            return new \DateTimeImmutable('now', $tz);
        }

        // Parse incoming date with its own timezone (ex: UTC from ISO string)
        $dt = new \DateTimeImmutable($date);

        // Convert to Asia/Kolkata
        return $dt->setTimezone($tz);
    }
}