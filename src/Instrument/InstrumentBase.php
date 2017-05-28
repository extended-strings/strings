<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Instrument;

use ExtendedStrings\Strings\Note;

abstract class InstrumentBase implements InstrumentInterface
{
    protected $defaultStopLength;

    private $stringFrequencies = [];
    private $stopLength;

    /**
     * Violin constructor.
     *
     * @param array|null $stringFrequencies
     * @param float      $stopLength
     */
    public function __construct(array $stringFrequencies = null, float $stopLength = null)
    {
        if (null === $stringFrequencies) {
            $stringFrequencies = array_map(function ($name) {
                return Note::fromName($name)->getFrequency();
            }, $this->getDefaultNames());
        }
        if (null === $stopLength) {
            $stopLength = $this->getDefaultStopLength();
        }

        $this->stringFrequencies = $stringFrequencies;
        $this->stopLength = $stopLength;
    }

    /**
     * @return string[] The default strings of the instrument, as note names
     *                  (e.g. 'A4', 'D3').
     */
    abstract protected function getDefaultNames(): array;

    /**
     * @return float The default stop length of the instrument (mm).
     */
    abstract protected function getDefaultStopLength(): float;

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
    public function getStopLength(): float
    {
        return $this->stopLength;
    }
}
