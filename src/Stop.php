<?php

declare(strict_types=1);

namespace ExtendedStrings\Harmonics;

class Stop
{
    private $stringLength;

    /**
     * @param float $stringLength
     */
    public function __construct(float $stringLength)
    {
        if ($stringLength > 1 || $stringLength < 0) {
            throw new \InvalidArgumentException('String length cannot be more than 1 or less than 0');
        }

        $this->stringLength = $stringLength;
    }

    /**
     * @return float
     */
    public function getStringLength(): float
    {
        return $this->stringLength;
    }

    /**
     * @return bool
     */
    public function isOpen(): bool
    {
        return Math::isZero(1 - $this->stringLength);
    }
}
