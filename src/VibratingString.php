<?php

declare(strict_types=1);

namespace ExtendedStrings\Harmonics;

class VibratingString
{
    private $frequency;

    /**
     * @param float $frequency
     */
    public function __construct(float $frequency)
    {
        $this->frequency = $frequency;
    }

    /**
     * @param float $stringLength
     *
     * @return float
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
     * @param float $stringLength
     *
     * @return float
     */
    public function getHarmonicSoundingFrequency(float $stringLength = 1.0): float
    {
        return Math::isZero($stringLength)
            ? 0
            : $this->frequency * self::getHarmonicNumber($stringLength);
    }

    /**
     * @param float $frequency
     *
     * @return float
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
     * @param float $cents
     *   The number of cents between the open string and the stop.
     *
     * @return float
     *   The length of the remaining vibrating string.
     */
    private function centsToStringLength(float $cents): float
    {
        return 1 / pow(2, $cents / 1200);
    }

    /**
     * @param float $stringLength
     *
     * @throws \InvalidArgumentException
     *
     * @return int
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
     * @param int $limit
     *
     * @return float[]
     */
    public static function getHarmonicSeries(int $limit): array
    {
        $series = [];
        $base = 0;
        for ($denominator = 1; $denominator <= $limit; $denominator++) {
            $base = $series[$denominator] = $base + 1 / $denominator;
        }

        return $series;
    }
}
