<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

use ExtendedStrings\Strings\Instrument\InstrumentInterface;

class HarmonicCalculator
{
    /**
     * Returns a list of possible harmonics that produce a given sounding note.
     *
     * @param Note                $soundingNote The desired sounding note of
     *                                          the harmonic.
     * @param InstrumentInterface $instrument   The instrument.
     * @param float               $tolerance    The maximum deviation (cents)
     *                                          between the desired sounding
     *                                          note and a natural harmonic.
     * @param float               $maxDistance  The maximum distance (mm)
     *                                          between the stops.
     *
     * @return Harmonic[]
     */
    public function getHarmonicsForSoundingNote(Note $soundingNote, InstrumentInterface $instrument, float $tolerance = 50.0, float $maxDistance = 120.0): array
    {
        $harmonics = [];
        foreach ($instrument->getStringFrequencies() as $stringFrequency) {
            $string = new VibratingString($stringFrequency);
            $harmonics = array_merge(
                $harmonics,
                $this->findNaturalHarmonics($soundingNote, $string, $tolerance),
                $this->findArtificialHarmonics($soundingNote, $string)
            );
        }

        $harmonics = array_filter($harmonics, function (Harmonic $harmonic) use ($instrument, $maxDistance) {
            $distance = $harmonic->isNatural()
                ? 0
                : ($harmonic->getBaseStop() - $harmonic->getHalfStop()) * $instrument->getStringLength();

            return $distance < $maxDistance;
        });

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
            // Convert harmonic number to the sounding frequency.
            $candidateFrequency = $string->getHarmonicSoundingFrequency(1 / $number);

            // Calculate the difference in cents between the natural harmonic
            // frequency and the desired sounding note.
            $difference = abs(Cent::frequencyToCents($candidateFrequency) - $soundingCents);

            if ($difference <= $tolerance) {
                $stringLengths = Harmonic::getStringLengthsFromNumber($number, true);
                foreach ($stringLengths as $stringLength) {
                    $harmonics[] = new Harmonic($stringLength, 1.0, $string);
                }
            }
        }

        return $harmonics;
    }
}
