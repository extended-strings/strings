<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

interface InstrumentStringInterface extends VibratingStringInterface
{
    /**
     * Returns the number of the string on the instrument (1 is the highest).
     *
     * @return int
     */
    public function getNumber(): int;

    /**
     * Returns the maximum vibrating length of the string (in mm).
     *
     * @return float
     */
    public function getLength(): float;
}
