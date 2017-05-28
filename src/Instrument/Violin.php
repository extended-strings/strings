<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Instrument;

class Violin extends InstrumentBase implements InstrumentInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultNames(): array
    {
        return ['E5', 'A4', 'D4', 'G3'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultStopLength(): float
    {
        return 196.5;
    }
}
