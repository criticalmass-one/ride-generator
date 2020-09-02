<?php declare(strict_types=1);

namespace App\RideGenerator;

use App\Model\CityCycle;
use App\RideCalculator\RideCalculator;
use App\RideCalculator\RideCalculatorInterface;
use App\RideNamer\RideNamerListInterface;

abstract class AbstractRideGenerator implements RideGeneratorInterface
{
    protected array $dateTimeList = [];

    protected array $rideList = [];

    protected RideNamerListInterface $rideNamerList;

    public function __construct(RideNamerListInterface $rideNamerList)
    {
        $this->rideNamerList = $rideNamerList;
    }

    public function setDateTime(\DateTime $dateTime): RideGeneratorInterface
    {
        $this->dateTimeList = [$dateTime];

        return $this;
    }

    public function addDateTime(\DateTime $dateTime): RideGeneratorInterface
    {
        $this->dateTimeList[] = $dateTime;

        return $this;
    }

    public function setDateTimeList(array $dateTimeList): RideGeneratorInterface
    {
        $this->dateTimeList = $dateTimeList;

        return $this;
    }

    public function getRideList(): array
    {
        return $this->rideList;
    }

    protected function hasRideAlreadyBeenCreated(CityCycle $cityCycle, \DateTime $startDateTime): bool
    {
        return false;
        //$endDateTime = DateTimeUtil::getMonthEndDateTime($startDateTime);

        //$existingRides = $this->doctrine->getRepository(Ride::class)->findRidesByCycleInInterval($cityCycle, $startDateTime, $endDateTime);

        //return count($existingRides) > 0;
    }

    protected function getRideCalculatorForCycle(CityCycle $cityCycle): RideCalculatorInterface
    {
        if (($rideCalculatorFqcn = $cityCycle->getRideCalculatorFqcn()) && class_exists($rideCalculatorFqcn)) {
            return new $rideCalculatorFqcn($this->rideNamerList);
        }

        return new RideCalculator($this->rideNamerList);
    }

    public abstract function execute(): RideGeneratorInterface;
}
