<?php declare(strict_types=1);

namespace App\RideCalculator;

use App\Model\CityCycle;
use App\Model\Ride;
use App\RideNamer\RideNamerListInterface;

abstract class AbstractRideCalculator implements RideCalculatorInterface
{
    protected int $month;

    protected int $year;

    protected CityCycle $cycle;

    protected RideNamerListInterface $rideNamerList;

    protected ?\DateTimeZone $timezone = null;

    public function __construct(RideNamerListInterface $rideNamerList)
    {
        $this->rideNamerList = $rideNamerList;
    }

    public function setTimezone(\DateTimeZone $timezone): RideCalculatorInterface
    {
        $this->timezone = $timezone;

        return $this;
    }

    public function setCycle(CityCycle $cityCycle): RideCalculatorInterface
    {
        $this->cycle = $cityCycle;

        return $this;
    }

    public function setYear(int $year): RideCalculatorInterface
    {
        $this->year = $year;

        return $this;
    }

    public function setMonth(int $month): RideCalculatorInterface
    {
        $this->month = $month;

        return $this;
    }

    public abstract function execute(): ?Ride;
}
