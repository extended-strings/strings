<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Tests;

use ExtendedStrings\Strings\Harmonic;
use ExtendedStrings\Strings\Stop;
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
                Stop::fromFrequency($frequency, $string)->getStringLength(),
                Stop::fromFrequency($frequency * 4/3, $string)->getStringLength(),
                $frequency * 4
            ];

            // Nodes one fifth apart: one octave above the upper note.
            $expectations[] = [
                Stop::fromFrequency($frequency, $string)->getStringLength(),
                Stop::fromFrequency($frequency * 3/2, $string)->getStringLength(),
                $frequency * 3/2 * 2
            ];

            // Nodes one octave apart: same as the upper note.
            $expectations[] = [
                Stop::fromFrequency($frequency, $string)->getStringLength(),
                Stop::fromFrequency($frequency * 2, $string)->getStringLength(),
                $frequency * 2
            ];
        }

        $actual = array_map(function ($expectation) use ($string) {
            list($stop, $halfStop,) = $expectation;
            $harmonic = new Harmonic(new Stop($halfStop), new Stop($stop), $string);

            return [$stop, $halfStop, $harmonic->getSoundingFrequency()];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testInvalidHarmonic()
    {
        $this->expectException(\InvalidArgumentException::class);
        new Harmonic(new Stop(.2), new Stop(.1), new VibratingString(440.0));
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

    public function testStringLengthsFromNumber()
    {
        $expected = [
            1 => [1],
            2 => [1/2],
            3 => [1/3, 2/3],
            4 => [1/4, 3/4],
            5 => [1/5, 2/5, 3/5, 4/5],
            6 => [1/6, 5/6],
            7 => [1/7, 2/7, 3/7, 4/7, 5/7, 6/7],
            8 => [1/8, 3/8, 5/8, 7/8],
            9 => [1/9, 2/9, 4/9, 5/9, 7/9, 8/9],
            10 => [1/10, 3/10, 7/10, 9/10],
        ];
        $actual = [];
        foreach (array_keys($expected) as $number) {
            $actual[$number] = Harmonic::getStringLengthsFromNumber($number, true);
        }
        $this->assertEquals($expected, $actual);
    }

    public function testGetNumber()
    {
        /**
         * @var array $expectations
         * An array of arrays each containing a numerator, denominator, and
         * expected harmonic number.
         */
        $expectations = [
            [1, 2, 2],
            [1, 3, 3],
            [2, 3, 3],
            [3, 3, 1],
            [1, 4, 4],
            [2, 4, 2],
            [3, 4, 4],
            [4, 4, 1],
            [1, 5, 5],
            [2, 5, 5],
            [3, 5, 5],
            [4, 5, 5],
            [5, 5, 1],
            [1, 6, 6],
            [2, 6, 3],
            [3, 6, 2],
            [4, 6, 3],
            [5, 6, 6],
            [6, 6, 1],
            [99, 100, 100]
        ];
        $actual = array_map(function ($expectation) {
            list($n, $d,) = $expectation;
            $harmonic = new Harmonic(new Stop($n / $d), null, new VibratingString(100.0));

            return [$n, $d, $harmonic->getNumber()];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testGetSoundingFrequency()
    {
        $string = new VibratingString(131.0);
        /**
         * @var array $expectations
         * An array of arrays each containing a string length (as numerator and
         * denominator), and expected sounding frequency.
         *
         * @see http://www.phy.mtu.edu/~suits/overtone.html
         */
        $expectations = [
            [1, 1, 131],
            [1, 2, 262], [2, 4, 262],
            [1, 3, 393], [2, 3, 393], [2, 6, 393],
            [1, 4, 524], [3, 4, 524],
            [1, 5, 655], [2, 5, 655], [3, 5, 655], [4, 5, 655],
            [1, 6, 786], [5, 6, 786],
            [1, 7, 917], [2, 7, 917], [3, 7, 917], [4, 7, 917], [5, 7, 917], [6, 7, 917],
            [99, 100, 13100], [198, 200, 13100], [396, 400, 13100],
        ];
        $actual = array_map(function ($expectation) use ($string) {
            list($n, $d,) = $expectation;
            $harmonic = new Harmonic(new Stop($n / $d), null, $string);

            return [$n, $d, round($harmonic->getSoundingFrequency(), 2)];
        }, $expectations
        );
        $this->assertEquals($expectations, $actual);
    }
}
