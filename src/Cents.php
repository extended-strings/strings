<?php

namespace ExtendedStrings\Harmonics;

class Cents
{
    /**
     * @param float $stringLength
     *
     * @return float
     */
    public static function stringLengthToCents(float $stringLength): float
    {
        return self::frequenciesToCents(1, $stringLength);
    }

    /**
     * @param float $lower
     * @param float $upper
     *
     * @return float
     */
    public static function frequenciesToCents(float $lower, float $upper): float
    {
        return Math::isZero($upper) ? 0 : 1200 * (log($lower / $upper) / log(2));
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

    /**
     * @param float $cents
     *
     * @return float
     */
    public static function centsToStringLength(float $cents): float
    {
        return pow(2, $cents / 1200);
    }
}
