<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class Harmonic
{
    private $halfStop;
    private $baseStop;
    private $number;
    private $string;

    /**
     * Harmonic constructor.
     *
     * @param Stop $halfStop
     *     The harmonic-pressure stop.
     * @param Stop|null $baseStop
     *     The base stop (defaults to an open string).
     * @param VibratingStringInterface $string
     *     The string.
     */
    public function __construct(Stop $halfStop, Stop $baseStop = null, VibratingStringInterface $string)
    {
        $baseStop = $baseStop ?: new Stop(1.0);
        if ($halfStop->getStringLength() > $baseStop->getStringLength()) {
            throw new \InvalidArgumentException("The half-stop cannot be lower than the base stop.");
        }

        $this->baseStop = $baseStop;
        $this->halfStop = $halfStop;
        $this->number = intval(1 / Math::gcd(1, $halfStop->getStringLength() / $baseStop->getStringLength()));
        $this->string = $string;
    }

    /**
     * Returns the sounding frequency of the harmonic (in Hz).
     *
     * @return float
     */
    public function getSoundingFrequency(): float
    {
        return $this->baseStop->getFrequency($this->string) * $this->number;
    }

    /**
     * Returns the string lengths that produce a given harmonic number.
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

    /**
     * Returns the harmonic-pressure stop.
     *
     * @return Stop
     */
    public function getHalfStop(): Stop
    {
        return $this->halfStop;
    }

    /**
     * Returns the base stop.
     *
     * @return Stop
     */
    public function getBaseStop(): Stop
    {
        return $this->baseStop;
    }

    /**
     * Returns the string.
     *
     * @return VibratingStringInterface
     */
    public function getString(): VibratingStringInterface
    {
        return $this->string;
    }

    /**
     * Returns whether this is a natural harmonic.
     *
     * @return bool
     */
    public function isNatural(): bool
    {
        return Math::isZero(1 - $this->baseStop->getStringLength());
    }

    /**
     * Returns the harmonic number.
     *
     * @return int The harmonic number.
     */
    public function getNumber(): int
    {
        return $this->number;
    }
}
