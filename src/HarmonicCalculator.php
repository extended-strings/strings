<?php

declare(strict_types = 1);

namespace ExtendedStrings\Strings;

class HarmonicCalculator
{
    private $minStopDistance = 1.0;
    private $maxStopDistance = 120.0;
    private $minBowedDistance = 20.0;
    private $maxSoundingNoteDifference = 50.0;

    /**
     * Returns a list of possible harmonics that produce a given sounding note.
     *
     * @param Note                $soundingNote The desired sounding note of
     *                                          the harmonic.
     * @param InstrumentInterface $instrument   The instrument.
     *
     * @return Harmonic[]
     */
    public function findHarmonics(Note $soundingNote, InstrumentInterface $instrument): array
    {
        $harmonics = [];
        foreach ($instrument->getStrings() as $string) {
            $harmonics = array_merge(
                $harmonics,
                $this->findNaturalHarmonics($soundingNote, $string),
                $this->findArtificialHarmonics($soundingNote, $string)
            );
        }

        $harmonics = array_filter($harmonics, function (Harmonic $harmonic) {
            return $this->validatePhysicalDistance($harmonic);
        });

        return $harmonics;
    }

    /**
     * Set constraints on the physical distance between harmonic stops.
     *
     * @param float $minStopDistance  The minimum distance between stops (mm).
     * @param float $maxStopDistance  The maximum distance between stops (mm).
     * @param float $minBowedDistance The minimum distance between the upper
     *                                harmonic stop and the bridge (mm).
     */
    public function setPhysicalDistanceConstraints(float $minStopDistance, float $maxStopDistance, float $minBowedDistance)
    {
        $this->minStopDistance = $minStopDistance;
        $this->maxStopDistance = $maxStopDistance;
        $this->minBowedDistance = $minBowedDistance;
    }

    /**
     * Set the max difference between the sounding note and natural harmonics.
     *
     * @param float $difference The difference in cents (default: 50.0).
     */
    public function setMaxSoundingNoteDifference(float $difference)
    {
        $this->maxSoundingNoteDifference = $difference;
    }

    /**
     * Check that the harmonic is within the configured distance constraints.
     *
     * @see HarmonicCalculator::setPhysicalDistanceConstraints()
     *
     * @param \ExtendedStrings\Strings\Harmonic $harmonic
     *
     * @return bool
     */
    private function validatePhysicalDistance(Harmonic $harmonic): bool
    {
        if (!$harmonic->isNatural()) {
            $distance = $this->getPhysicalDistanceBetweenStops($harmonic);

            if ($distance < $this->minStopDistance || $distance > $this->maxStopDistance) {
                return false;
            }
        }

        return $this->getBowedDistance($harmonic) >= $this->minBowedDistance;
    }

    /**
     * Find the physical distance between the stops of a harmonic.
     *
     * @param \ExtendedStrings\Strings\Harmonic $harmonic
     *
     * @return float
     */
    private function getPhysicalDistanceBetweenStops(Harmonic $harmonic): float
    {
        return ($harmonic->getBaseStop()->getStringLength() - $harmonic->getHalfStop()->getStringLength())
            * $this->getPhysicalStringLength($harmonic);
    }

    /**
     * Find the physical distance between a harmonic's half-stop and the bridge.
     *
     * @param \ExtendedStrings\Strings\Harmonic $harmonic
     *
     * @return float
     */
    private function getBowedDistance(Harmonic $harmonic): float
    {
        return $harmonic->getHalfStop()->getStringLength() * $this->getPhysicalStringLength($harmonic);
    }

    /**
     * Find the physical length of the harmonic's string.
     *
     * @param Harmonic $harmonic
     *
     * @return float
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
     *
     * @return Harmonic[]
     */
    private function findNaturalHarmonics(Note $soundingNote, InstrumentStringInterface $string): array
    {
        $harmonics = [];
        $soundingCents = $soundingNote->getCents();
        foreach (range(1, 8) as $number) {
            // Convert harmonic number to the sounding frequency.
            $candidateFrequency = (new Stop(1 / $number))->getFrequency($string);

            // Calculate the difference in cents between the natural harmonic
            // frequency and the desired sounding note.
            $difference = abs(Cent::frequencyToCents($candidateFrequency) - $soundingCents);

            if ($difference <= $this->maxSoundingNoteDifference) {
                $stringLengths = Harmonic::getStringLengthsFromNumber($number, true);
                foreach ($stringLengths as $stringLength) {
                    $harmonics[] = new Harmonic(new Stop($stringLength), null, $string);
                }
            }
        }

        return $harmonics;
    }
}
