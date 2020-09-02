<?php declare(strict_types=1);

namespace App\RideCalculator;

use App\Model\CityCycle;
use App\Model\Ride;

interface RideCalculatorInterface
{
    public function setTimezone(\DateTimeZone $timezone): RideCalculatorInterface;

    public function setCycle(CityCycle $cycle): RideCalculatorInterface;

    public function setMonth(int $month): RideCalculatorInterface;

    public function setYear(int $year): RideCalculatorInterface;

    public function execute(): ?Ride;
}
