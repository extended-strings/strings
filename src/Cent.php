<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class Cent
{
    /**
     * Returns the number of cents between two frequencies.
     *
     * @param float $lower The lower frequency (in Hz).
     * @param float $upper The upper frequency (in Hz).
     *
     * @return float
     */
    public static function frequenciesToCents(float $lower, float $upper): float
    {
        return Math::isZero($lower) ? 0 : 1200 * (log($upper / $lower) / log(2));
    }

    /**
     * Calculates the frequency of a number of cents above a base frequency.
     *
     * @param float $cents A number of cents.
     * @param float $base  The base frequency (in Hz).
     *
     * @return float The calculated frequency (in Hz).
     */
    public static function centsToFrequency(float $cents, float $base): float
    {
        return $base * pow(2, $cents / 1200);
    }
}
