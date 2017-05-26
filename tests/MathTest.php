<?php

declare(strict_types=1);

namespace ExtendedStrings\Strings\Tests;

use ExtendedStrings\Strings\Math;
use PHPUnit\Framework\TestCase;

class MathTest extends TestCase
{
    public function testIsZero()
    {
        $expected = [
            [0, true],
            [.0, true],
            [.1, false],
            [.01, false],
            [.00000001, false],
            [.000000001, false],
            [.0000000001, false],
            [.00000000009, true],
        ];
        $actual = array_map(function ($expectation) {
            list ($value,) = $expectation;

            return [$value, Math::isZero($value)];
        }, $expected);
        $this->assertEquals($expected, $actual);
    }

    public function testGcd()
    {
        $expected = [
            [1, 3, 1.0],
            [.1, .3, .1],
            [2, 6, 2.0],
            [.2, .6, .2],
            [6, 12, 6.0],
            [.6, 1.2, .6],
        ];
        $actual = array_map(function ($expectation) {
            list ($a, $b,) = $expectation;

            return [$a, $b, Math::gcd($a, $b)];
        }, $expected);
        $this->assertEquals($expected, $actual);
    }
}
