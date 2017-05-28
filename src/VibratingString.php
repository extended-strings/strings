<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class VibratingString
{
    private $frequency;

    /**
     * VibratingString constructor.
     *
     * @param float $frequency The open string frequency (in Hz).
     */
    public function __construct(float $frequency)
    {
        $this->frequency = $frequency;
    }

    /**
     * Calculate the frequency for a stop.
     *
     * @param float $stringLength The length of the vibrating part of the
     *                            string, as a fraction of the whole string.
     *
     * @return float A frequency (in Hz).
     */
    public function getStoppedFrequency(float $stringLength = 1.0): float
    {
        if (Math::isZero($stringLength)) {
            return 0;
        }
        $centsOverString = Cent::frequenciesToCents($stringLength, 1);

        return Cent::centsToFrequency($centsOverString, $this->frequency);
    }

    /**
     * Find the sounding frequency of a harmonic-pressure stop on this string.
     *
     * @param float $stringLength The string length, as a fraction, between the
     *                            open string and the stop (either side: both
     *                            .2 and .8 would produce the same result).
     *
     * @return float The sounding frequency of a harmonic-pressure stop at the
     *               given string length.
     */
    public function getHarmonicSoundingFrequency(float $stringLength = 1.0): float
    {
        return Math::isZero($stringLength)
            ? 0
            : $this->frequency * self::getHarmonicNumber($stringLength);
    }

    /**
     * Calculate the length of this string needed to produce a given frequency.
     *
     * @param float $frequency The frequency for which to calculate the string
     *                         length.
     *
     * @return float
     *   The length of the vibrating part of the string, as a fraction of the
     *   whole string's length.
     */
    public function getStringLength(float $frequency): float
    {
        if (Math::isZero($frequency)) {
            return 0;
        }
        $centsOverString = Cent::frequenciesToCents($this->frequency, $frequency);

        return $this->centsToStringLength($centsOverString);
    }

    /**
     * Convert a string length to a harmonic number.
     *
     * @param float $stringLength The string length between the open string and
     *                            the harmonic-pressure stop (either side), as a
     *                            fraction.
     *
     * @throws \InvalidArgumentException If the stop is not a sounding natural
     *                                   harmonic.
     *
     * @return int The harmonic number.
     */
    public static function getHarmonicNumber(float $stringLength): int
    {
        $number = intval(1 / Math::gcd(1, $stringLength));
        if ($number > 100) {
            throw new \InvalidArgumentException(sprintf('Invalid string length for a harmonic: %f', $stringLength));
        }

        return $number;
    }

    /**
     * Convert a number of cents to a string length.
     *
     * @param float $cents The number of cents between the open string and the
     *                     stopped pitch.
     *
     * @return float The length of the vibrating string, as a fraction of the
     * whole string's length.
     */
    private function centsToStringLength(float $cents): float
    {
        return 1 / pow(2, $cents / 1200);
    }
}
