<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Instrument;

interface InstrumentInterface
{
    /**
     * Returns the instrument's string frequencies (Hz), in descending order.
     *
     * @return float[]
     */
    public function getStringFrequencies(): array;

    /**
     * Returns the vibrating string length of the instrument (in mm).
     *
     * @return float
     */
    public function getStringLength(): float;
}
