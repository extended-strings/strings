<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class Instrument implements InstrumentInterface
{
    private $strings;

    /**
     * Instrument constructor.
     *
     * @param InstrumentStringInterface[] $strings
     */
    public function __construct(array $strings)
    {
        $this->strings = $strings;
    }

    /**
     * @param string[] $stringNames
     * @param float    $length
     *
     * @return self
     */
    public static function fromNames(array $stringNames, float $length = 500.0): self
    {
        $strings = [];
        $number = 1;
        foreach ($stringNames as $name) {
            $frequency = Note::fromName($name)->getFrequency();
            $strings[] = new InstrumentString($frequency, $length, $number++);
        }

        return new self($strings);
    }

    /**
     * @param string $preset
     *
     * @return self
     */
    public static function fromPreset(string $preset): self
    {
        switch ($preset) {
            case 'violin':
                $names = ['E5', 'A4', 'D4', 'G3'];
                $length = 325;
                break;

            case 'viola':
                $names = ['A4', 'D3', 'G3', 'C3'];
                $length = 410;
                break;

            case 'cello':
                $names = ['A3', 'D2', 'G2', 'C2'];
                $length = 690;
                break;

            case 'guitar':
                $names = ['E4', 'B3', 'G3', 'D3', 'A2', 'E2'];
                $length = 650;
                break;

            case 'double bass':
                $names = ['E1', 'A1', 'D2', 'G2'];
                $length = 1140;
                break;

            default:
                throw new \InvalidArgumentException(sprintf('Preset not found: %s', $preset));
        }

        return self::fromNames($names, $length);
    }

    /**
     * {@inheritdoc}
     */
    public function getStrings(): array
    {
        return $this->strings;
    }
}
