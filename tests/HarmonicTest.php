<?php

declare(strict_types=1);

namespace ExtendedStrings\Strings\Tests;

use ExtendedStrings\Strings\Harmonic;
use ExtendedStrings\Strings\VibratingString;
use PHPUnit\Framework\TestCase;

class HarmonicTest extends TestCase
{
    public function testGetSoundingPitch()
    {
        $string = new VibratingString(440.0);
        /**
         * @var array $expectations
         * An array of arrays each containing a stop length, a half-stop length,
         * and expected real frequency.
         */
        // Natural harmonics.
        $expectations = [
            [1, 1.0, 440.0], // A4, open string
            [1, 1/2, 880.0], // A4, 2nd harmonic (A5)
            [1, 1/3, 1320.0], [1, 2/3, 1320.0], // A4, 3rd harmonic (E6)
            [1, 1/4, 1760.0], // A4, 4th harmonic (A6)
            [1, 1/5, 2200.0], // A4, 5th harmonic (C#7)
            [1, 1/6, 2640.0], // A4, 6th harmonic (E7)
            [1, 1/7, 3080.0], // A4, 7th harmonic (~G7)
            [1, 1/8, 3520.0], // A4, 8th harmonic (A7)
            [1, 1/9, 3960.0], // A4, 9th harmonic (~B7)
            [1, 1/10, 4400.0], // A4, 10th harmonic (~C#8)
            [1, 1/11, 4840.0], // A4, 11th harmonic (~D8)
            [1, 1/12, 5280.0], // A4, 12th harmonic (~E8)
            [1, 1/13, 5720.0], // A4, 13th harmonic (~F8)
            [1, 1/14, 6160.0], // A4, 14th harmonic (~G8)
            [1, 1/15, 6600.0], // A4, 15th harmonic (~G#8)
            [1, 1/16, 7040.0], [1, 5/16, 7040.0], // A4, 16th harmonic (A8)
        ];

        // Artificial harmonics.
        for ($i = 1; $i <= 5; $i++) {
            // Test one tone at a time above A4.
            $frequency = 440.0 * $i * 9/8;

            // Nodes one fourth apart: two octaves above the lower note.
            $expectations[] = [
                $string->getStringLength($frequency),
                $string->getStringLength($frequency * 4/3),
                $frequency * 4
            ];

            // Nodes one fifth apart: one octave above the upper note.
            $expectations[] = [
                $string->getStringLength($frequency),
                $string->getStringLength($frequency * 3/2),
                $frequency * 3/2 * 2
            ];

            // Nodes one octave apart: same as the upper note.
            $expectations[] = [
                $string->getStringLength($frequency),
                $string->getStringLength($frequency * 2),
                $frequency * 2
            ];
        }

        $actual = array_map(function ($expectation) use ($string) {
            list($stop, $halfStop,) = $expectation;
            $harmonic = new Harmonic($halfStop, $stop);

            return [$stop, $halfStop, $harmonic->getSoundingPitch($string)];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testInvalidHarmonic()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Harmonic(.2, .1);
    }

    public function testSeries()
    {
        /**
         * @var array $expected
         * The beginning of the harmonic series.
         */
        $expected = [
            1 => 1,
            2 => 1 + 1/2,
            3 => 1 + 1/2 + 1/3,
            4 => 1 + 1/2 + 1/3 + 1/4,
            5 => 1 + 1/2 + 1/3 + 1/4 + 1/5,
            6 => 1 + 1/2 + 1/3 + 1/4 + 1/5 + 1/6,
            7 => 1 + 1/2 + 1/3 + 1/4 + 1/5 + 1/6 + 1/7,
            8 => 1 + 1/2 + 1/3 + 1/4 + 1/5 + 1/6 + 1/7 + 1/8,
        ];
        $this->assertEquals($expected, Harmonic::getSeries(8));
    }
}
