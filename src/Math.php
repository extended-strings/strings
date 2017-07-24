<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class Math
{
    const EPSILON = 0.0000000001;

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
        return $x === 0 || abs($x) < self::EPSILON;
    }

    /**
     * Tests whether a number is greater than another.
     *
     * @param float $a
     * @param float $b
     *
     * @return bool
     */
    public static function isGreaterThan(float $a, float $b): bool
    {
        return $a > $b && $a - $b > self::EPSILON;
    }
}
