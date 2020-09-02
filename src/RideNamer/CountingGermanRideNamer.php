<?php declare(strict_types=1);

namespace App\RideNamer;

use App\Model\Ride;
use App\RideNamer\CountingRideNamer\AbstractCountingRideNamer;

class CountingGermanRideNamer extends AbstractCountingRideNamer
{
    public function generateTitle(Ride $ride): string
    {
        $cityTitle = $ride->getCity()->getTitle();

        return sprintf('%d. %s', ($this->countRides($ride) + 1), $cityTitle);
    }
}
