<?php declare(strict_types=1);

namespace App\RideNamer;

use App\Model\Ride;
use App\RideNamer\CountingRideNamer\AbstractCountingRideNamer;

class CountingEnglishRideNamer extends AbstractCountingRideNamer
{
    public function generateTitle(Ride $ride): string
    {
        $cityTitle = $ride->getCity()->getTitle();

        return sprintf('%dth %s', ($this->countRides($ride) + 1), $cityTitle);
    }
}
