<?php

namespace ExtendedStrings\Harmonics;

class Harmonic
{
    private $stop;
    private $halfStop;

    /**
     * @param \ExtendedStrings\Harmonics\Stop $stop
     * @param \ExtendedStrings\Harmonics\Stop $halfStop
     */
    public function __construct(Stop $stop, Stop $halfStop)
    {
        if ($halfStop->getStringLength() > $stop->getStringLength()) {
            throw new \InvalidArgumentException('Half-stop cannot be lower than base stop');
        }

        $this->stop = $stop;
        $this->halfStop = $halfStop;
    }

    /**
     * @return bool
     */
    public function isNatural(): bool
    {
        return $this->stop->isOpen();
    }

    /**
     * @return \ExtendedStrings\Harmonics\Stop
     */
    public function getStop(): Stop
    {
        return $this->stop;
    }

    /**
     * @return \ExtendedStrings\Harmonics\Stop
     */
    public function getHalfStop(): Stop
    {
        return $this->halfStop;
    }
}
