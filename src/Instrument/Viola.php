<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Instrument;

class Viola extends InstrumentBase implements InstrumentInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultNames(): array
    {
        return ['A4', 'D3', 'G3', 'C3'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultStringLength(): float
    {
        return 410;
    }
}
