<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

interface InstrumentInterface
{
    /**
     * Returns the instrument's strings.
     *
     * @return VibratingStringInterface[]
     */
    public function getStrings(): array;
}
