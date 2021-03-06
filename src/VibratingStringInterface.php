<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

interface VibratingStringInterface
{
    /**
     * Returns the frequency of the open string (in Hz).
     *
     * @return float
     */
    public function getFrequency(): float;

    /**
     * Returns the physical length of the string (in mm).
     *
     * @return float
     */
    public function getPhysicalLength(): float;
}
