<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class InstrumentString extends VibratingString implements InstrumentStringInterface
{
    private $physicalLength;
    private $number;

    public function __construct(float $frequency, float $physicalLength, int $number)
    {
        $this->number = $number;
        $this->physicalLength = $physicalLength;

        parent::__construct($frequency);
    }

    /**
     * {@inheritdoc}
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * {@inheritdoc}
     */
    public function getPhysicalLength(): float
    {
        return $this->physicalLength;
    }
}
