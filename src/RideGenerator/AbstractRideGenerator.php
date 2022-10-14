<?php declare(strict_types=1);

namespace App\RideGenerator;

use App\Model\CityCycle;
use App\RideCalculator\RideCalculator;
use App\RideCalculator\RideCalculatorInterface;
use App\RideCalculator\RideCalculatorManagerInterface;
use App\RideNamer\RideNamerListInterface;
use Carbon\Carbon;
use Symfony\Component\Validator\Validator\ValidatorInterface;

abstract class AbstractRideGenerator implements RideGeneratorInterface
{
    protected array $dateTimeList = [];

    protected array $rideList = [];

    public function __construct(protected RideNamerListInterface $rideNamerList, protected ValidatorInterface $validator, protected RideCalculatorManagerInterface $rideCalculatorManager)
    {
    }

    public function setDateTime(Carbon $dateTime): RideGeneratorInterface
    {
        $this->dateTimeList = [$dateTime];

        return $this;
    }

    public function addDateTime(Carbon $dateTime): RideGeneratorInterface
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

    protected function hasRideAlreadyBeenCreated(CityCycle $cityCycle, Carbon $startDateTime): bool
    {
        return false;
        //$endDateTime = DateTimeUtil::getMonthEndDateTime($startDateTime);

        //$existingRides = $this->doctrine->getRepository(Ride::class)->findRidesByCycleInInterval($cityCycle, $startDateTime, $endDateTime);

        //return count($existingRides) > 0;
    }

    protected function getRideCalculatorForCycle(CityCycle $cityCycle): RideCalculatorInterface
    {
        /** TODO */
        if (($rideCalculatorFqcn = $cityCycle->getRideCalculatorFqcn()) && class_exists($rideCalculatorFqcn)) {
            return new $rideCalculatorFqcn($this->rideNamerList, $this->validator);
        }

        return new RideCalculator($this->rideNamerList, $this->validator);
    }

    public abstract function execute(): RideGeneratorInterface;
}
