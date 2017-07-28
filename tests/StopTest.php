<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings\Tests;

use ExtendedStrings\Strings\Stop;
use ExtendedStrings\Strings\VibratingString;
use PHPUnit\Framework\TestCase;

class StopTest extends TestCase
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
            [1, 1, 440.0],
            [4, 5, 550.0],
            [2, 3, 660.0],
            [1, 2, 880.0],
            [1, 4, 1760.0],
            [1, 5, 2200.0],
        ];
        $actual = array_map(function ($expectation) use ($string) {
            list($n, $d,) = $expectation;
            $stop = new Stop($n / $d);

            return [$n, $d, round($stop->getFrequency($string), 2)];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testGetStringLength()
    {
        $expectations = [
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
            return [$frequency, Stop::fromFrequency($frequency, $string)->getStringLength()];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testStringLengthValidation()
    {
        $stops = [
            new Stop(0.1),
            new Stop(0.5),
            new Stop(1.0),
        ];
        $this->assertEquals(3, count($stops));
        $this->expectException('\\InvalidArgumentException');
        new Stop(0.0);
    }
}
