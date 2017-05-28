<?php

declare(strict_types=1);

namespace ExtendedStrings\Strings;

class Harmonic
{
    private $halfStop;
    private $baseStop;

    /**
     * @param float $halfStop
     * @param float $baseStop
     */
    public function __construct(float $halfStop, float $baseStop = 1.0)
    {
        if ($halfStop > $baseStop) {
            throw new \InvalidArgumentException("The half-stop's string length cannot be longer than the base stop's.");
        }

        $this->baseStop = $baseStop;
        $this->halfStop = $halfStop;
    }

    /**
     * @param \ExtendedStrings\Strings\VibratingString $string
     *
     * @return float
     */
    public function getSoundingPitch(VibratingString $string): float
    {
        // Transpose the half-stop onto the new string length, which was formed
        // by the stop.
        $pseudoString = new VibratingString($string->getStoppedFrequency($this->baseStop));
        $pseudoHalfStop = $this->halfStop / $this->baseStop;

        return $pseudoString->getHarmonicSoundingFrequency($pseudoHalfStop);
    }

    /**
     * @param int  $number
     * @param bool $exclusive
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
