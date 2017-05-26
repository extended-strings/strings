<?php

declare(strict_types=1);

namespace ExtendedStrings\Harmonics\Tests;

use ExtendedStrings\Harmonics\VibratingString;
use PHPUnit\Framework\TestCase;

class VibratingStringTest extends TestCase
{
    public function testGetStoppedFrequency()
    {
        $string = new VibratingString(440.0);
        /**
         * @var array $expectations
         * An array of arrays each containing a numerator, denominator, and
         * expected frequency.
         */
        $expectations = [
            [0, 1, 0],
            [1, 1, 440.0],
            [4, 5, 550.0],
            [2, 3, 660.0],
            [1, 2, 880.0],
            [1, 4, 1760.0],
            [1, 5, 2200.0],
        ];
        $actual = array_map(function ($expectation) use ($string) {
            list($n, $d,) = $expectation;
            return [$n, $d, round($string->getStoppedFrequency($n / $d), 2)];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testGetHarmonicNumber()
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
            return [$n, $d, VibratingString::getHarmonicNumber($n / $d)];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testInvalidHarmonic()
    {
        $this->expectException(\InvalidArgumentException::class);
        VibratingString::getHarmonicNumber(199 / 200);
    }

    public function testGetHarmonicSoundingFrequency()
    {
        $string = new VibratingString(131.0);
        /**
         * @var array $expectations
         * An array of arrays each containing a numerator, denominator, and
         * expected sounding frequency.
         *
         * @see http://www.phy.mtu.edu/~suits/overtone.html
         */
        $expectations = [
            [0, 1, 0],
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
            return [$n, $d, round($string->getHarmonicSoundingFrequency($n / $d), 2)];
        }, $expectations
        );
        $this->assertEquals($expectations, $actual);
    }

    public function testHarmonicSeries()
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
        $this->assertEquals($expected, VibratingString::getHarmonicSeries(8));
    }

    public function testGetStringLength()
    {
        $expectations = [
            [0, 0],
            [440.0, 1],
            [550.0, 4/5],
            [660.0, 2/3],
            [880.0, 1/2],
            [1760.0, 1/4],
            [2200.0, 1/5],
        ];
        $string = new VibratingString(440.0);
        $actual = array_map(function ($expectation) use ($string) {
            list($frequency, ) = $expectation;
            return [$frequency, $string->getStringLength($frequency)];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }
}
