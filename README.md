# strings

[![Build Status](https://travis-ci.org/extended-strings/strings.svg?branch=master)](https://travis-ci.org/extended-strings/strings) [![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/extended-strings/strings/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/extended-strings/strings/?branch=master)

Calculates string lengths, frequencies, intervals, harmonics, etc.

## Example

```php
<?php

require_once 'vendor/autoload.php';

use ExtendedStrings\Strings\HarmonicCalculator;
use ExtendedStrings\Strings\Instrument;
use ExtendedStrings\Strings\Note;

// Find all the possible harmonics on a violin for the sounding pitch D5.
$harmonics = (new HarmonicCalculator())
    ->findHarmonics(Note::fromName('D5'), Instrument::fromPreset('violin'));

foreach ($harmonics as $harmonic) {
    $string = $harmonic->getString();
    $stringNote = Note::fromFrequency($string->getFrequency());
    $baseNote = Note::fromFrequency($harmonic->getBaseStop()->getFrequency($string));
    $harmonicNote = Note::fromFrequency($harmonic->getHalfStop()->getFrequency($string));
    $soundingNote = Note::fromFrequency($harmonic->getSoundingFrequency());

    echo "String: $stringNote\n";
    if ($harmonic->isNatural()) {
        echo "  Harmonic pressure on: $harmonicNote\n";
    } else {
        echo "  Artificial harmonic: $baseNote : $harmonicNote\n";
    }
    echo "  Sounding pitch: $soundingNote\n";
    echo "\n";
}
```

Result:

```
String: D4
  Harmonic pressure on: D5
  Sounding pitch: D5

String: G3
  Harmonic pressure on: D5 +2c
  Sounding pitch: D5 +2c

String: G3
  Harmonic pressure on: D4 +2c
  Sounding pitch: D5 +2c

String: G3
  Artificial harmonic: D4 : D5
  Sounding pitch: D5
```
