<?php declare(strict_types=1);

namespace App\RideCalculator;

class RideCalculatorManager implements RideCalculatorManagerInterface
{
    protected array $rideCalculatorList;

    public function __construct()
    {

    }

    public function addRideCalculator(RideCalculatorInterface $rideCalculator): self
    {
        $this->rideCalculatorList[$rideCalculator::class] = $rideCalculator;

        return $this;
    }
}
