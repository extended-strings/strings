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
     * Converts a frequency to a number of cents above C4.
     *
     * @param float $frequency The frequency to convert.
     * @param float $A4        The reference frequency of A4 (defaults to 440).
     *
     * @return float
     */
    public static function frequencyToCents(float $frequency, float $A4 = 440.0): float
    {
        return self::frequenciesToCents($A4, $frequency) + 900;
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
