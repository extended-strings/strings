<?php

declare(strict_types=1);

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

    /**
     * Internal constructor: use one of the factory methods to create a Note.
     *
     * @param string $name       The note name (A-G).
     * @param string $accidental The accidental (one of the Note::ACCIDENTAL_
     *                           constants).
     * @param int    $octave     The octave, in scientific pitch notation.
     */
    private function __construct(string $name, string $accidental, int $octave)
    {
        $this->name = $name;
        $this->accidental = $accidental;
        $this->octave = $octave;
    }

    /**
     * Factory to create a Note from a note name.
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
        if (preg_match('/\-?[0-9]+$/i', $name, $matches)) {
            $octave = intval(ltrim($matches[0]));
            $name = substr($name, 0, strlen($name) - strlen($matches[0]));
        }
        $accidental = self::normalizeAccidental($name);

        return new self($noteName, $accidental, $octave);
    }

    /**
     * Factory to create a Note from a number of cents.
     *
     * @param int      $cents                A number of cents above C4.
     * @param string[] $preferredAccidentals A list of accidentals in order of
     *                                       preference. This will be merged
     *                                       with a default list.
     *
     * @return self
     */
    public static function fromCents(int $cents, array $preferredAccidentals = []): self
    {
        $octave = (int) floor($cents / 1200) + 4;
        $centsWithoutOctave = $cents - (($octave - 4) * 1200);
        $preferredAccidentals = array_merge($preferredAccidentals, self::$preferredAccidentals);
        foreach ($preferredAccidentals as $accidental) {
            $accidentalCents = self::$accidentalCents[$accidental];
            if (($name = array_search($centsWithoutOctave - $accidentalCents, self::$names, true)) !== false) {
                return new self((string) $name, $accidental, $octave);
            }
        }

        throw new \InvalidArgumentException(sprintf('Failed to find note name for cents: %d', $cents));
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

    /**
     * @return int The number of cents above C4.
     */
    public function getCents(): int
    {
        return self::$names[$this->name]
            + self::$accidentalCents[$this->accidental]
            + (($this->octave - 4) * 1200);
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return sprintf('%s%s%d', $this->name, $this->accidental, $this->octave);
    }
}
