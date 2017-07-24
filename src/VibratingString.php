<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class VibratingString implements VibratingStringInterface
{
    private $frequency;
    private $physicalLength;

    /**
     * VibratingString constructor.
     *
     * @param float $frequency      The open string frequency (in Hz).
     * @param float $physicalLength The physical length of the string (in mm).
     */
    public function __construct(float $frequency, float $physicalLength = 500.0)
    {
        $this->frequency = $frequency;
        $this->physicalLength = $physicalLength;
    }

    /**
     * {@inheritdoc}
     */
    public function getFrequency(): float
    {
        return $this->frequency;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhysicalLength(): float
    {
        return $this->physicalLength;
    }
}
