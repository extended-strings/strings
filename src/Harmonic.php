<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class Harmonic
{
    private $halfStop;
    private $baseStop;

    /**
     * Harmonic constructor.
     *
     * @param float $halfStop The string length of the harmonic-pressure stop.
     * @param float $baseStop The string length of the base stop (defaults to
     *                        1.0, the open string).
     */
    public function __construct(float $halfStop, float $baseStop = 1.0)
    {
        VibratingString::validateStringLength($halfStop);
        VibratingString::validateStringLength($baseStop);
        if ($halfStop > $baseStop) {
            throw new \InvalidArgumentException("The half-stop's string length cannot be longer than the base stop's.");
        }

        $this->baseStop = $baseStop;
        $this->halfStop = $halfStop;
    }

    /**
     * Returns the sounding frequency of the harmonic, on a given string.
     *
     * @param \ExtendedStrings\Strings\VibratingString $string
     *
     * @return float
     */
    public function getSoundingFrequency(VibratingString $string): float
    {
        // Transpose the half-stop onto the new string length, which was formed
        // by the stop.
        $pseudoString = new VibratingString($string->getStoppedFrequency($this->baseStop));
        $pseudoHalfStop = $this->halfStop / $this->baseStop;

        return $pseudoString->getHarmonicSoundingFrequency($pseudoHalfStop);
    }

    /**
     * Returns the string lengths that produce the given harmonic number.
     *
     * @param int  $number    The harmonic number.
     * @param bool $exclusive When enabled, equivalent lengths will only be
     *                        returned for the lowest harmonic number, e.g. the
     *                        string length 0.5 will only be returned for
     *                        harmonic 2 (not for harmonics 4, 6, 8, etc.).
     *
     * @return float[]
     */
    public static function getStringLengthsFromNumber(int $number, bool $exclusive = false): array
    {
        $harmonics = [];
        for ($numerator = 1; $numerator <= $number; $numerator++) {
            if (!$exclusive || $numerator === 1 || (int) Math::gcd($numerator, $number) === 1) {
                $harmonics[] = $numerator / $number;
            }
        }

        return $harmonics;
    }

    /**
     * Returns the harmonic series.
     *
     * @param int $limit
     *
     * @return float[]
     */
    public static function getSeries(int $limit): array
    {
        $series = [];
        $base = 0;
        for ($denominator = 1; $denominator <= $limit; $denominator++) {
            $base = $series[$denominator] = $base + 1 / $denominator;
        }

        return $series;
    }
}
