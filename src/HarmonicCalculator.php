<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class HarmonicCalculator
{
    private $minDistance = 1.0;
    private $maxDistance = 120.0;
    private $minBowedDistance = 20.0;

    /**
     * Returns a list of possible harmonics that produce a given sounding note.
     *
     * @param Note                $soundingNote The desired sounding note of
     *                                          the harmonic.
     * @param InstrumentInterface $instrument   The instrument.
     * @param float               $tolerance    The maximum deviation (cents)
     *                                          between the desired sounding
     *                                          note and a natural harmonic.
     *
     * @return Harmonic[]
     */
    public function findHarmonics(Note $soundingNote, InstrumentInterface $instrument, float $tolerance = 50.0): array
    {
        $harmonics = [];
        foreach ($instrument->getStrings() as $string) {
            $harmonics = array_merge(
                $harmonics,
                $this->findNaturalHarmonics($soundingNote, $string, $tolerance),
                $this->findArtificialHarmonics($soundingNote, $string)
            );
        }

        $harmonics = array_filter($harmonics, function (Harmonic $harmonic) {
            return $this->validateDistance($harmonic);
        });

        return $harmonics;
    }

    /**
     * Set the minimum and maximum distance between harmonic stops (in mm).
     *
     * @param float $minDistance
     * @param float $maxDistance
     * @param float $minBowedDistance
     */
    public function setDistanceConstraints(float $minDistance, float $maxDistance, float $minBowedDistance)
    {
        $this->minDistance = $minDistance;
        $this->maxDistance = $maxDistance;
        $this->minBowedDistance = $minBowedDistance;
    }

    /**
     * Check that the harmonic is within the configured distance constraints.
     *
     * @see HarmonicCalculator::setDistanceConstraints()
     *
     * @param \ExtendedStrings\Strings\Harmonic $harmonic
     *
     * @return bool
     */
    private function validateDistance(Harmonic $harmonic): bool
    {
        $physicalLength = $this->getPhysicalStringLength($harmonic);
        $basePhysical = $harmonic->getBaseStop()->getStringLength() * $physicalLength;
        $halfStopPhysical = $harmonic->getHalfStop()->getStringLength() * $physicalLength;
        $distance = $basePhysical - $halfStopPhysical;
        $bowedDistance = $halfStopPhysical;

        return (
            $harmonic->isNatural()
            || ($distance >= $this->minDistance && $distance <= $this->maxDistance)
          ) && $bowedDistance >= $this->minBowedDistance;
    }

    /**
     * Find the physical length of the harmonic's string.
     */
    private function getPhysicalStringLength(Harmonic $harmonic): float
    {
      $string = $harmonic->getString();

      return $string instanceof InstrumentStringInterface
          ? $string->getPhysicalLength()
          : 500.0;
    }

    /**
     * Find the artificial harmonics that produce the given sounding note.
     *
     * @param \ExtendedStrings\Strings\Note                     $soundingNote
     * @param \ExtendedStrings\Strings\VibratingStringInterface $string
     *
     * @return Harmonic[]
     */
    private function findArtificialHarmonics(Note $soundingNote, VibratingStringInterface $string): array
    {
        $harmonics = [];
        $soundingNoteFrequency = $soundingNote->getFrequency();
        $stringFrequency = $string->getFrequency();
        foreach (range(6, 2) as $number) {
            $fundamental = $soundingNoteFrequency / $number;
            if ($fundamental > $stringFrequency) {
                $baseStop = Stop::fromFrequency($fundamental, $string);
                $ratio = ($number - 1) / $number;
                $halfStop = new Stop($ratio * $baseStop->getStringLength());

                $harmonics[] = new Harmonic($halfStop, $baseStop, $string);
            }
        }

        return $harmonics;
    }

    /**
     * Find the natural harmonics that produce the given sounding note.
     *
     * @param \ExtendedStrings\Strings\Note            $soundingNote
     * @param \ExtendedStrings\Strings\InstrumentStringInterface $string
     * @param float                                    $tolerance
     *
     * @return Harmonic[]
     */
    private function findNaturalHarmonics(Note $soundingNote, InstrumentStringInterface $string, float $tolerance = 50.0): array
    {
        $harmonics = [];
        $soundingCents = $soundingNote->getCents();
        foreach (range(1, 8) as $number) {
            // Convert harmonic number to the sounding frequency.
            $candidateFrequency = (new Stop(1 / $number))->getFrequency($string);

            // Calculate the difference in cents between the natural harmonic
            // frequency and the desired sounding note.
            $difference = abs(Cent::frequencyToCents($candidateFrequency) - $soundingCents);

            if ($difference <= $tolerance) {
                $stringLengths = Harmonic::getStringLengthsFromNumber($number, true);
                foreach ($stringLengths as $stringLength) {
                    $harmonics[] = new Harmonic(new Stop($stringLength), null, $string);
                }
            }
        }

        return $harmonics;
    }
}
