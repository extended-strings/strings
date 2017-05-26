<?php

declare(strict_types=1);

namespace ExtendedStrings\Harmonics;

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
     * @param \ExtendedStrings\Harmonics\VibratingString $string
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
}
