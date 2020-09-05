<?php declare(strict_types=1);

namespace App\RideCalculator;

interface RideCalculatorManagerInterface
{
    public function addRideCalculator(RideCalculatorInterface $rideCalculator): self;
}
