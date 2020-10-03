<?php declare(strict_types=1);

namespace App\RideGenerator;

use App\Model\City;
use App\Model\CityCycle;
use Carbon\Carbon;

class CityRideGenerator extends AbstractRideGenerator implements CityRideGeneratorInterface
{
    /** @var array $cityList */
    protected $cityList;

    public function addCity(City $city): RideGeneratorInterface
    {
        $this->cityList[] = $city;

        return $this;
    }

    public function setCityList(array $cityList): RideGeneratorInterface
    {
        $this->cityList = $cityList;

        return $this;
    }

    public function execute(): RideGeneratorInterface
    {
        foreach ($this->cityList as $city) {
            /** @var Carbon $dateTime */
            foreach ($this->dateTimeList as $dateTime) {
                $startDateTime = $dateTime->startOfMonth();

                $cycles = $this->findCyclesForCity($city, $startDateTime);

                $createdRides = $this->processCityCycles($cycles, $startDateTime);

                $this->rideList = array_merge($this->rideList, $createdRides);
            }
        }

        return $this;
    }

    protected function findCyclesForCity(City $city, Carbon $startDateTime): array
    {
        $endDateTime = $startDateTime->endOfMonth();

        return $this->doctrine->getRepository(CityCycle::class)->findByCity(
            $city,
            $startDateTime,
            $endDateTime
        );
    }

    protected function processCityCycles(array $cycles, Carbon $startDateTime): array
    {
        $cycles = $this->removeCreatedCycles($cycles, $startDateTime);

        $rideList = [];

        foreach ($cycles as $cycle) {
            $ride = $this->getRideCalculatorForCycle($cycle)
                ->setCycle($cycle)
                ->setMonth((int)$startDateTime->format('m'))
                ->setYear((int)$startDateTime->format('Y'))
                ->execute();

            if ($ride && DateTimeValidator::isValidRide($cycle, $ride)) {
                $rideList[] = $ride;
            }
        }

        return $rideList;
    }

    protected function removeCreatedCycles(array $cycles, Carbon $startDateTime): array
    {
        foreach ($cycles as $key => $cycle) {
            if ($this->hasRideAlreadyBeenCreated($cycle, $startDateTime)) {
                unset($cycles[$key]);
            }
        }

        return $cycles;
    }
}
