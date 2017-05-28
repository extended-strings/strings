<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

use ExtendedStrings\Strings\Instrument\InstrumentInterface;

class HarmonicCalculator
{
    /**
     * @param \ExtendedStrings\Strings\Note                           $soundingNote
     * @param \ExtendedStrings\Strings\Instrument\InstrumentInterface $instrument
     * @param float                                                   $tolerance
     *
     * @return Harmonic[]
     */
    public function getHarmonicsForSoundingNote(Note $soundingNote, InstrumentInterface $instrument, float $tolerance = 50.0): array
    {
        $harmonics = [];
        foreach ($instrument->getStringFrequencies() as $stringFrequency) {
            $string = new VibratingString($stringFrequency);
            $harmonics += $this->findNaturalHarmonics($soundingNote, $string, $tolerance)
                + $this->findArtificialHarmonics($soundingNote, $string);
        }

        return $harmonics;
    }

    private function findArtificialHarmonics(Note $soundingNote, VibratingString $string): array
    {
        $harmonics = [];
        $soundingNoteFrequency = $soundingNote->getFrequency();
        $stringFrequency = $string->getStoppedFrequency();
        foreach (range(6, 2) as $number) {
            $fundamental = $soundingNoteFrequency / $number;
            if ($fundamental > $stringFrequency) {
                $baseStop = $string->getStringLength($fundamental);
                $halfStop = (($number - 1) / $number) * $baseStop;

                $harmonics[] = new Harmonic($halfStop, $baseStop, $string);
            }
        }

        return $harmonics;
    }

    private function findNaturalHarmonics(Note $soundingNote, VibratingString $string, float $tolerance = 50.0): array
    {
        $harmonics = [];
        $soundingCents = $soundingNote->getCents();
        foreach (range(1, 8) as $number) {
            // Convert harmonic number to (possible sounding) frequency.
            $candidateFrequency = $string->getHarmonicSoundingFrequency(1 / $number);

            // Convert (possible sounding) frequency to cents above C4, for comparison.
            $candidate = Note::fromFrequency($candidateFrequency, 440.0, [$soundingNote->getAccidental()]);
            $difference = abs($candidate->getCents() - $soundingCents);

            if ($difference < $tolerance) {
                $stringLengths = Harmonic::getStringLengthsFromNumber($number, true);
                foreach ($stringLengths as $stringLength) {
                    $harmonics[] = new Harmonic($stringLength, 1.0, $string);
                }
            }
        }

        return $harmonics;
    }
}
