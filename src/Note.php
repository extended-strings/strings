<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class Note
{
    const ACCIDENTAL_NATURAL = '';
    const ACCIDENTAL_SHARP = '#';
    const ACCIDENTAL_FLAT = 'b';
    const ACCIDENTAL_DOUBLE_SHARP = 'x';
    const ACCIDENTAL_DOUBLE_FLAT = 'bb';
    const ACCIDENTAL_QUARTER_SHARP = '+';
    const ACCIDENTAL_QUARTER_FLAT = '-';
    const ACCIDENTAL_THREE_QUARTER_SHARP = '#+';
    const ACCIDENTAL_THREE_QUARTER_FLAT = 'b-';

    private static $accidentalPatterns = [
        '' => self::ACCIDENTAL_NATURAL,
        "([fb]|\u{266D}|flat)" => self::ACCIDENTAL_FLAT,
        "([s#]|\u{266F}|sharp)" => self::ACCIDENTAL_SHARP,
        '(\-|quarter[ -]flat)' => self::ACCIDENTAL_QUARTER_FLAT,
        '(\+|quarter[ -]sharp)' => self::ACCIDENTAL_QUARTER_SHARP,
        '(bb|double[ -]flat)' => self::ACCIDENTAL_DOUBLE_FLAT,
        '(##|x|double[ -]sharp)' => self::ACCIDENTAL_DOUBLE_SHARP,
        '(b\-|(three|3)[ -]quarter[ -]flat)' => self::ACCIDENTAL_THREE_QUARTER_FLAT,
        '(#\+|(three|3)[ -]quarter[ -]sharp)' => self::ACCIDENTAL_THREE_QUARTER_SHARP,
    ];

    private static $accidentalCents = [
        self::ACCIDENTAL_NATURAL => 0,
        self::ACCIDENTAL_FLAT => -100,
        self::ACCIDENTAL_SHARP => 100,
        self::ACCIDENTAL_QUARTER_FLAT => -50,
        self::ACCIDENTAL_QUARTER_SHARP => 50,
        self::ACCIDENTAL_DOUBLE_FLAT => -200,
        self::ACCIDENTAL_DOUBLE_SHARP => 200,
        self::ACCIDENTAL_THREE_QUARTER_FLAT => -150,
        self::ACCIDENTAL_THREE_QUARTER_SHARP => 150,
    ];

    private static $preferredAccidentals = [
        self::ACCIDENTAL_NATURAL,
        self::ACCIDENTAL_SHARP,
        self::ACCIDENTAL_FLAT,
        self::ACCIDENTAL_QUARTER_SHARP,
        self::ACCIDENTAL_QUARTER_FLAT,
        self::ACCIDENTAL_DOUBLE_SHARP,
        self::ACCIDENTAL_DOUBLE_FLAT,
        self::ACCIDENTAL_THREE_QUARTER_FLAT,
        self::ACCIDENTAL_THREE_QUARTER_SHARP,
    ];

    private static $names = [
        'C' => 0,
        'D' => 200,
        'E' => 400,
        'F' => 500,
        'G' => 700,
        'A' => 900,
        'B' => 1100,
    ];

    private $name;
    private $accidental;
    private $octave;
    private $difference;

    /**
     * Internal constructor. Use one of the factory methods to create a Note.
     *
     * @see Note::fromCents()
     * @see Note::fromFrequency()
     * @see Note::fromName()
     *
     * @param string $name         The note name (A-G).
     * @param string $accidental   The accidental (one of the Note::ACCIDENTAL_
     *                             constants).
     * @param int    $octave       The octave, in scientific pitch notation.
     * @param float  $difference   The note's difference in cents from 12-TET.
     */
    private function __construct(string $name, string $accidental, int $octave, float $difference)
    {
        $this->name = $name;
        $this->accidental = $accidental;
        $this->octave = $octave;
        $this->difference = $difference;
    }

    /**
     * Instantiate a Note from a number of cents.
     *
     * @param float    $cents                A number of cents above C4.
     * @param string[] $preferredAccidentals A list of accidentals in order of
     *                                       preference. This will be merged
     *                                       with a default list.
     *
     * @return self
     */
    public static function fromCents(float $cents, array $preferredAccidentals = []): self
    {
        $rounded = (int) round($cents / 50) * 50;
        $difference = $cents - $rounded;
        $octave = (int) floor($rounded / 1200) + 4;
        $centsWithoutOctave = $rounded - (($octave - 4) * 1200);
        $preferredAccidentals = array_merge($preferredAccidentals, self::$preferredAccidentals);
        foreach ($preferredAccidentals as $accidental) {
            $accidentalCents = self::$accidentalCents[$accidental];
            if (($name = array_search($centsWithoutOctave - $accidentalCents, self::$names, true)) !== false) {
                return new self((string) $name, $accidental, $octave, $difference);
            }
        }

        throw new \InvalidArgumentException(sprintf('Failed to find note name for cents: %d', $cents)); // @codeCoverageIgnore
    }

    /**
     * Instantiate a Note from a note name.
     *
     * @param string $name A note name with an accidental and an octave in
     *                     scientific pitch notation, e.g. C#4 or Eb5.
     *
     * @return \ExtendedStrings\Strings\Note
     */
    public static function fromName(string $name): self
    {
        $original = $name;
        if (!preg_match('/^[a-g]/i', $name, $matches)) {
            throw new \InvalidArgumentException(sprintf('Invalid note name: %s', $original));
        }
        $noteName = strtoupper($matches[0]);
        $name = substr($name, strlen($matches[0]));
        if (preg_match('/^\-[0-9]+$/i', $name)) {
            throw new \InvalidArgumentException(sprintf(
                'Ambiguous note: %s (does "-" mean a quarter-flat or a negative?)',
                $original
            ));
        }
        $octave = 4;
        $difference = 0;
        if (preg_match('/\/?(\-?[0-9]+)?( ([\+-][0-9]+)c)?$/i', $name, $matches)) {
            if (isset($matches[1])) {
                $octave = intval($matches[1]);
            }
            if (isset($matches[3])) {
                $difference = intval($matches[3]);
            }
            $name = substr($name, 0, strlen($name) - strlen($matches[0]));
        }
        $accidental = self::normalizeAccidental($name);

        return new self($noteName, $accidental, $octave, $difference);
    }

    /**
     * Instantiate a Note from a frequency.
     *
     * @param float $frequency            The frequency (in Hz).
     * @param float $A4                   The frequency of A4, for reference.
     * @param array $preferredAccidentals Some preferred accidentals.
     *
     * @return self
     */
    public static function fromFrequency($frequency, float $A4 = 440.0, array $preferredAccidentals = []): self
    {
        $cents = Cent::frequenciesToCents($A4, $frequency) + 900;

        return self::fromCents($cents, $preferredAccidentals);
    }

    /**
     * Returns the note as a number of cents above C4.
     *
     * @return float
     */
    public function getCents(): float
    {
        return self::$names[$this->name]
            + self::$accidentalCents[$this->accidental]
            + (($this->octave - 4) * 1200)
            + $this->difference;
    }

    /**
     * Returns the note as a frequency.
     *
     * @param float $A4 The frequency of A4 (in Hz), for reference.
     *
     * @return float
     */
    public function getFrequency(float $A4 = 440.0): float
    {
        return Cent::centsToFrequency($this->getCents() - 900, $A4);
    }

    /**
     * Returns a string representation of the note.
     *
     * @return string
     */
    public function __toString(): string
    {
        $output = sprintf('%s%s%d', $this->name, $this->accidental, $this->octave);
        if ((int) round($this->difference) !== 0) {
            $output .= sprintf(' %+dc', round($this->difference));
        }

        return $output;
    }

    /**
     * Returns the simple note name (one of A-G).
     *
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Returns the accidental (one of the Note::ACCIDENTAL_ constants).
     *
     * @return string
     */
    public function getAccidental(): string
    {
        return $this->accidental;
    }

    /**
     * Returns the octave (in scientific pitch notation).
     *
     * @return int
     */
    public function getOctave(): int
    {
        return $this->octave;
    }

    /**
     * Returns the difference between the note and its 12-TET form, in cents.
     *
     * @return float
     */
    public function getDifference(): float
    {
        return $this->difference;
    }

    /**
     * @param string $accidental
     *
     * @return string
     */
    private static function normalizeAccidental(string $accidental): string
    {
        $accidental = trim($accidental);

        foreach (self::$accidentalPatterns as $pattern => $replacement) {
            if (preg_match('/^' . $pattern . '$/i', $accidental)) {
                return $replacement;
            }
        }

        throw new \InvalidArgumentException(sprintf('Invalid accidental: %s', $accidental));
    }
}
