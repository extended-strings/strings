<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class InstrumentString extends VibratingString implements InstrumentStringInterface
{
    private $length;
    private $number;

    public function __construct(float $frequency, float $length, int $number)
    {
        $this->number = $number;
        $this->length = $length;

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
    public function getLength(): float
    {
        return $this->length;
    }
}
