<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Tests;

use ExtendedStrings\Strings\HarmonicCalculator;
use ExtendedStrings\Strings\Instrument;
use ExtendedStrings\Strings\Note;
use PHPUnit\Framework\TestCase;

class HarmonicCalculatorTest extends TestCase
{
    public function testFindHarmonics()
    {
        $instrument = Instrument::fromPreset('violin');
        $calculator = new HarmonicCalculator();

        // Sounding pitch (as a note name), and the possible harmonic positions
        // as an array of pairs of baseStop, halfStop string lengths, keyed by
        // the string name.
        $expectations = [
            ['A5', [
                'A' => [[1.0, .5]], // 2nd harmonic.
                'D' => [
                    [1.0, .33], // 3rd harmonic.
                    [1.0, .67], // 3rd harmonic.
                    [.67, .33], // Artificial harmonic (8ve apart), A4 and A5.
                ],
                'G' => [
                    [.89, .67], // Artificial harmonic (4th apart), A3 and D4.
                    [.67, .45], // Artificial harmonic (5th apart), D4 and A4.
                    [.45, .22], // Artificial harmonic (8ve apart), A4 and A5.
                ],
            ]],
            ['G5', [
                'G' => [
                    [1.0, 1/4], // 4th harmonic, on G5.
                    [1.0, 3/4], // 4th harmonic, on C4.
                    [3/4, 1/2], // Artificial harmonic (5th apart), C4 and G4.
                    [1/2, 1/4], // Artificial harmonic (8ve apart), G4 and G5.
                ],
            ]],
            ['G3', [
                'G' => [[1.0, 1.0]], // Open string.
            ]],
        ];

        $actual = array_map(function ($expectation) use ($instrument, $calculator) {
            list($soundingNoteName,) = $expectation;
            $result = [];
            $harmonics = $calculator->findHarmonics(Note::fromName($soundingNoteName), $instrument);
            foreach ($harmonics as $harmonic) {
                $stringName = Note::fromFrequency($harmonic->getString()->getFrequency())->getName();
                $result[$stringName][] = [
                    round($harmonic->getBaseStop()->getStringLength(), 2),
                    round($harmonic->getHalfStop()->getStringLength(), 2),
                ];
            }

            return [$soundingNoteName, $result];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }
}
