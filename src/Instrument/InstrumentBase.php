<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Instrument;

use ExtendedStrings\Strings\Note;

abstract class InstrumentBase implements InstrumentInterface
{
    protected $defaultStopLength;

    private $stringFrequencies = [];
    private $stringLength;

    /**
     * Violin constructor.
     *
     * @param array|null $stringFrequencies
     * @param float      $stringLength
     */
    public function __construct(array $stringFrequencies = null, float $stringLength = null)
    {
        if (null === $stringFrequencies) {
            $stringFrequencies = array_map(function ($name) {
                return Note::fromName($name)->getFrequency();
            }, $this->getDefaultNames());
        }
        if (null === $stringLength) {
            $stringLength = $this->getDefaultStringLength();
        }

        $this->stringFrequencies = $stringFrequencies;
        $this->stringLength = $stringLength;
    }

    /**
     * @return string[] The default strings of the instrument, as note names
     *                  (e.g. 'A4', 'D3').
     */
    abstract protected function getDefaultNames(): array;

    /**
     * @return float The default vibrating string length of the instrument (mm).
     */
    abstract protected function getDefaultStringLength(): float;

    /**
     * {@inheritdoc}
     */
    public function getStringFrequencies(): array
    {
        return $this->stringFrequencies;
    }

    /**
     * {@inheritdoc}
     */
    public function getStringLength(): float
    {
        return $this->stringLength;
    }
}
