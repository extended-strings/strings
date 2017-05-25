<?php

declare(strict_types=1);

namespace ExtendedStrings\Harmonics\Tests;

use ExtendedStrings\Harmonics\Cents;
use PHPUnit\Framework\TestCase;

class CentsTest extends TestCase
{
    public function testCentsToFrequency()
    {
        $base = 440.0;
        $expectedFrequencies = [
            0 => 440.0, // A4
            100 => 466.16,
            150 => 479.82, // B quarter-flat 4
            200 => 493.88,
            300 => 523.25,
            400 => 554.37,
            500 => 587.33,
            600 => 622.25,
            700 => 659.26,
            800 => 698.46,
            900 => 739.99,
            1000 => 783.99,
            1100 => 830.61,
            1200 => 880.0, // A5
            1900 => 1318.51, // E6
            2400 => 1760.0, // A6
            3100 => 2637.02, // E7
            3600 => 3520.0, // A7
            4800 => 7040.0, // A8
        ];
        $actualFrequencies = array_map(function ($cents) use ($base) {
            return round(Cents::centsToFrequency($cents, $base), 2);
        }, array_combine(array_keys($expectedFrequencies), array_keys($expectedFrequencies)));
        $this->assertEquals($expectedFrequencies, $actualFrequencies);
    }
}
