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
        ];
        $actual = array_map(function ($expectation) {
            list($name,) = $expectation;
            $note = Note::fromName($name);

            return [$name, $note->getCents()];
        }, $expectations);
        $this->assertEquals($expectations, $actual);
    }

    public function testAmbiguousName()
    {
        $this->assertEquals(-50, Note::fromName('C- 4')->getCents());
        $this->assertEquals(-9600, Note::fromName('C -4')->getCents());
        $this->expectExceptionMessage('Ambiguous note');
        Note::fromName('C-4');
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

    public function testInvalidCents()
    {
        $this->expectException(\InvalidArgumentException::class);
        echo Note::fromCents(1999);
    }
}
