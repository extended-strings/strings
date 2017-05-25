<?php

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
        return Math::isZero($stringLength)
            ? 0
            : Cents::centsToFrequency(Cents::stringLengthToCents($stringLength), $this->frequency);
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
            : $this->frequency / (1 / self::getHarmonicNumber($stringLength));
    }

    /**
     * @param float $frequency
     *
     * @return float
     */
    public function getStringLength(float $frequency): float
    {
        return Math::isZero($frequency)
            ? 0
            : Cents::centsToStringLength(Cents::frequenciesToCents($this->frequency, $frequency));
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
        $gcd = Math::gcd(1, $stringLength);
        if (1 / $gcd > 100) {
            throw new \InvalidArgumentException(sprintf('Not a harmonic node: %.2f', $stringLength));
        }

        return 1 / $gcd;
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
