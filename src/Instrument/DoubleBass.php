<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Instrument;

class DoubleBass extends InstrumentBase implements InstrumentInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultNames(): array
    {
        return ['E1', 'A1', 'D2', 'G2'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultStopLength(): float
    {
        return 1060;
    }
}
