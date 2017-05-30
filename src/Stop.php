<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class Stop
{
    private $stringLength;

    /**
     * Stop constructor.
     *
     * @param float $stringLength The length of string that is left vibrating,
     *                            as a fraction.
     */
    public function __construct(float $stringLength)
    {
        if ($stringLength < 0 || $stringLength > 1) {
            throw new \InvalidArgumentException(sprintf('Invalid string length: %f', $stringLength));
        }

        $this->stringLength = $stringLength;
    }

    /**
     * Create a Stop instance from a frequency over a string.
     *
     * @param float                                             $frequency
     * @param \ExtendedStrings\Strings\VibratingStringInterface $string
     *
     * @return \ExtendedStrings\Strings\Stop
     */
    public static function fromFrequency(float $frequency, VibratingStringInterface $string): self
    {
        if (Math::isZero($frequency)) {
            throw new \InvalidArgumentException(sprintf('Invalid frequency: %f', $frequency));
        }
        $centsOverString = Cent::frequenciesToCents($string->getFrequency(), $frequency);

        return new self(self::centsToStringLength($centsOverString));
    }

    /**
     * Returns the frequency of the stop (assuming normal pressure).
     *
     * @param VibratingStringInterface $string
     *
     * @return float
     */
    public function getFrequency(VibratingStringInterface $string): float
    {
        $centsOverString = Cent::frequenciesToCents($this->stringLength, 1);

        return Cent::centsToFrequency($centsOverString, $string->getFrequency());
    }

    /**
     * Returns the length of string that is left vibrating, as a fraction.
     *
     * @return float
     */
    public function getStringLength(): float
    {
        return $this->stringLength;
    }

    /**
     * Convert a number of cents over a string to a string length.
     *
     * @param float $cents The number of cents between the open string and the
     *                     stopped pitch.
     *
     * @return float The length of the vibrating string, as a fraction of the
     * whole string's length.
     */
    private static function centsToStringLength(float $cents): float
    {
        return 1 / pow(2, $cents / 1200);
    }
}
