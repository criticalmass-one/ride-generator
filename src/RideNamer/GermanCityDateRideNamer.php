<?php declare(strict_types=1);

namespace App\RideNamer;

use App\Model\Ride;

class GermanCityDateRideNamer implements RideNamerInterface
{
    public function generateTitle(Ride $ride): string
    {
        $cityTitle = $ride->getCity()->getTitle();
        $date = $ride->getDateTime()->format('d.m.Y');

        return sprintf('%s %s', $cityTitle, $date);
    }
}
