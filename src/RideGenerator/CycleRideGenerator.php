<?php declare(strict_types=1);

namespace App\RideGenerator;

use App\Model\CityCycle;
use Carbon\Carbon;

class CycleRideGenerator extends AbstractRideGenerator implements CycleRideGeneratorInterface
{
    /** @var array $cycleList */
    protected $cycleList = [];

    public function execute(): RideGeneratorInterface
    {
        /** @var Carbon $dateTime */
        foreach ($this->dateTimeList as $dateTime) {
            $startDateTime = $dateTime->startOfMonth();

            $createdRides = $this->processCycles($startDateTime);

            $this->rideList = array_merge($this->rideList, $createdRides);
        }

        return $this;
    }

    protected function processCycles(Carbon $startDateTime): array
    {
        $cycles = $this->removeCreatedCycles($startDateTime);

        $rideList = [];

        foreach ($cycles as $cycle) {
            $ride = $this->getRideCalculatorForCycle($cycle)
                ->setCycle($cycle)
                ->setMonth((int)$startDateTime->format('m'))
                ->setYear((int)$startDateTime->format('Y'))
                ->execute();

            if ($ride/* && DateTimeValidator::isValidRide($cycle, $ride)*/) {
                $rideList[] = $ride;
            }
        }

        return $rideList;
    }

    protected function removeCreatedCycles(Carbon $startDateTime): array
    {
        foreach ($this->cycleList as $key => $cycle) {
            if ($this->hasRideAlreadyBeenCreated($cycle, $startDateTime)) {
                unset($this->cycleList[$key]);
            }
        }

        return $this->cycleList;
    }

    public function addCycle(CityCycle $cityCycle): RideGeneratorInterface
    {
        $this->cycleList[] = $cityCycle;

        return $this;
    }

    public function setCycleList(array $cycleList): RideGeneratorInterface
    {
        $this->cycleList = $cycleList;

        return $this;
    }
}
