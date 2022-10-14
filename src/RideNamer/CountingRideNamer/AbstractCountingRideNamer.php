<?php declare(strict_types=1);

namespace App\RideNamer\CountingRideNamer;

use App\Model\Ride;
use App\RideNamer\RideNamerInterface;

abstract class AbstractCountingRideNamer implements RideNamerInterface
{
    public function __construct(protected RideCounterInterface $rideCounter)
    {
    }

    protected function countRides(Ride $ride): int
    {
        return $this->rideCounter->countRides($ride->getCity());
    }

    public function generateTitle(Ride $ride): string
    {
        $cityTitle = $ride->getCity()->getTitle();

        return sprintf('%dth %s', ($this->countRides($ride) + 1), $cityTitle);
    }
}
