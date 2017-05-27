<?php

declare(strict_types=1);

namespace ExtendedStrings\Strings\Tests;

use ExtendedStrings\Strings\Note;
use PHPUnit\Framework\TestCase;

class NoteTest extends TestCase
{
    public function testFromCents()
    {
        $expectations = [
            [1200, [], 'C5'],
            [1200, ['-', '#'], 'C5'],
            [1250, [], 'C+5'],
            [1300, [], 'C#5'],
            [1300, ['b'], 'Db5'],
            [1400, [], 'D5'],
            [1400, ['bb'], 'Ebb5'],
            [1450, [], 'D+5'],
            [1850, [], 'G-5'],
            [-1, [], 'C4 -1c'],
            [1785, [], 'F#5 -15c'],
        ];
        $actual = array_map(function ($expectation) {
            list($cents, $accidentalPreference,) = $expectation;
            $note = Note::fromCents($cents, $accidentalPreference);

            return [$cents, $accidentalPreference, $note->__toString()];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testGetCents()
    {
        $expectations = [
            ['C5', 1200],
            ['C+5', 1250],
            ['Db- 5', 1250],
            ['D5', 1400],
            ['D5 -2c', 1398],
            ['A4 +25c', 925],
        ];
        $actual = array_map(function ($expectation) {
            list($name,) = $expectation;
            $note = Note::fromName($name);

            return [$name, $note->getCents()];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testGetFrequency()
    {
        // See http://www.phy.mtu.edu/~suits/notefreqs.html for a reference.
        // Modern pitch on the left, Baroque pitch on the right.
        $expectations = [
            ['A0', 440.0, 27.5],
            ['Ab4', 440.0, 415.3],
            ['A4', 440.0, 440.0], ['A4', 415.3, 415.3],
            ['D5', 440.0, 587.33],
            ['D#5', 440.0, 622.25], ['E5', 415.3, 622.25],
            ['E5', 440.0, 659.26],
            ['A8', 440.0, 7040.0],
            ['B8', 440.0, 7902.13],
        ];
        $actual = array_map(function ($expectation) {
            list($name, $A4, ) = $expectation;
            $note = Note::fromName($name);

            return [$name, $A4, round($note->getFrequency($A4), 2)];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testFromFrequency()
    {
        $expectations = [
            ['A0', 440.0, 27.5],
            ['G#4', 440.0, 415.3],
            ['A4', 440.0, 440.0], ['A4', 415.3, 415.3],
            ['D5', 440.0, 587.33],
            ['D#5', 440.0, 622.25], ['E5', 415.3, 622.25],
            ['E5', 440.0, 659.26],
            ['A8', 440.0, 7040.0],
            ['B8', 440.0, 7902.13],
        ];
        $actual = array_map(function ($expectation) {
            list(, $A4, $frequency) = $expectation;
            $note = Note::fromFrequency($frequency, $A4);

            return [$note->__toString(), $A4, $frequency];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testAmbiguousName()
    {
        $this->assertEquals(-50, Note::fromName('C- 4')->getCents());
        $this->assertEquals(-9600, Note::fromName('C -4')->getCents());
        $this->expectExceptionMessage('Ambiguous note');
        Note::fromName('C-11');
    }

    public function testInvalidName()
    {
        $this->expectExceptionMessage('Invalid note name');
        Note::fromName('H');
    }

    public function testInvalidAccidental()
    {
        $this->expectExceptionMessage('Invalid accidental');
        Note::fromName('A&');
    }
}
