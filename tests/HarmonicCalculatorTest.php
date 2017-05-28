<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Tests;

use ExtendedStrings\Strings\HarmonicCalculator;
use ExtendedStrings\Strings\Instrument\Violin;
use ExtendedStrings\Strings\Note;
use PHPUnit\Framework\TestCase;

class HarmonicCalculatorTest extends TestCase
{
    public function testGetHarmonicsForInstrument()
    {
        $instrument = new Violin();
        $calculator = new HarmonicCalculator();

        // Sounding pitch (as a note name), and the possible harmonic positions
        // as an array of pairs of baseStop, halfStop string lengths, keyed by
        // the string name.
        $expectations = [
            ['G- 7', [
                'E' => [
                    [.87, .65], // Artificial (third) harmonic.
                    [.65, .43], // Artificial (fifth) harmonic.
                    [.43, .22], // Artificial (octave) harmonic.
                ],
                'A' => [ // 7th harmonics (all sounding slightly above G- 7).
                    [1.0, round(4/7, 2)],
                    [1.0, round(5/7, 2)],
                    [1.0, round(6/7, 2)],
                ],
            ]],
            ['A5', [
                'A' => [[1.0, .5]], // 2nd harmonic.
                'D' => [[1.0, round(2/3, 2)]], // 3rd harmonic.
                'G' => [[.45, .22]], // Artificial (octave) harmonic.
            ]],
            ['A4', [
                'A' => [[1.0, 1.0]], // Open string.
            ]],
            ['G3', [
                'G' => [[1.0, 1.0]], // Open string.
            ]],
        ];

        $actual = array_map(function ($expectation) use ($instrument, $calculator) {
            list($soundingNoteName,) = $expectation;
            $result = [];
            $harmonics = $calculator->getHarmonicsForSoundingNote(Note::fromName($soundingNoteName), $instrument);
            foreach ($harmonics as $harmonic) {
                $stringName = Note::fromFrequency($harmonic->getString()->getStoppedFrequency())->getName();
                $result[$stringName][] = [
                    round($harmonic->getBaseStop(), 2),
                    round($harmonic->getHalfStop(), 2),
                ];
            }

            return [$soundingNoteName, $result];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }
}
