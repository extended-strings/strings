<?php

declare(strict_types=1);

namespace ExtendedStrings\Harmonics;

/**
 * A class containing functions useful for floating-point calculations.
 */
class Math
{
    const PRECISION = 10;

    /**
     * Returns the greatest common divisor of two floats.
     *
     * @param float $a
     * @param float $b
     *
     * @return float
     */
    public static function gcd(float $a, float $b): float
    {
        return self::isZero($b) ? $a : self::gcd($b, fmod($a, $b));
    }

    /**
     * Tests whether a float is zero.
     *
     * @param float $x
     *
     * @return bool
     */
    public static function isZero(float $x): bool
    {
        return $x === 0 || abs($x) < pow(10, - self::PRECISION);
    }
}
