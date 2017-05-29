<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Instrument;

class Guitar extends InstrumentBase implements InstrumentInterface
{
    /**
     * {@inheritdoc}
     */
    protected function getDefaultNames(): array
    {
        return ['E4', 'B3', 'G3', 'D3', 'A2', 'E2'];
    }

    /**
     * {@inheritdoc}
     */
    protected function getDefaultStringLength(): float
    {
        return 650;
    }
}
