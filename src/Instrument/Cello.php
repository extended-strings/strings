<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Instrument;

class Cello extends InstrumentBase implements InstrumentInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultNames(): array
    {
        return ['A3', 'D2', 'G2', 'C2'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultStopLength(): float
    {
        return 400;
    }
}
