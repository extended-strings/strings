<?php

declare(strict_types=1);

namespace ExtendedStrings\Strings;

class Cent
{
    /**
     * @param float $lower
     * @param float $upper
     *
     * @return float
     *   The number of cents between the two frequencies.
     */
    public static function frequenciesToCents(float $lower, float $upper): float
    {
        return Math::isZero($lower) ? 0 : 1200 * (log($upper / $lower) / log(2));
    }

    /**
     * @param float $cents
     * @param float $base
     *
     * @return float
     */
    public static function centsToFrequency(float $cents, float $base): float
    {
        return $base * pow(2, $cents / 1200);
    }
}
