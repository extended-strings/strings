<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class VibratingString implements VibratingStringInterface
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
     * Returns the frequency of the open string (in Hz).
     *
     * @return float
     */
    public function getFrequency(): float
    {
        return $this->frequency;
    }
}
