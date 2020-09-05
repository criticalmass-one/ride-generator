<?php declare(strict_types=1);

namespace App\RideCalculator;

use App\DateTimeValidator\DateTimeValidator;
use App\Model\CityCycle;
use App\Model\Ride;
use Carbon\Carbon;
use Carbon\CarbonInterval;
use Carbon\CarbonTimeZone;

class FrankfurtRideCalculator extends RideCalculator
{
    public function execute(): ?Ride
    {
        $dateTimeSpec = sprintf('%d-%d-01', $this->year, $this->month);
        $dateTime = new Carbon($dateTimeSpec);

        $cityTimeZone = new CarbonTimeZone($this->cycle->getCity()->getTimezone());
        $rideDateTime = new Carbon($dateTimeSpec, $cityTimeZone);

        $ride = $this->createRide($this->cycle, $rideDateTime);

        // yeah, first create ride and then check if it is matching the cycle range
        if ($ride && DateTimeValidator::isValidRide($this->cycle, $ride)) {
            return $ride;
        }

        return null;
    }

    protected function calculateDate(CityCycle $cityCycle, Ride $ride, Carbon $startDateTime): Ride
    {
        $dayInterval = new CarbonInterval('P1D');
        $sundayToFridayInterval = new CarbonInterval('P5D');

        $dateTime = clone $startDateTime;

        // first we look for the first sunday of the month
        while ($dateTime->format('w') != CityCycle::DAY_SUNDAY) {
            $dateTime->add($dayInterval);
        }

        // and then we add five days to get the friday ride date
        $dateTime->add($sundayToFridayInterval);

        $ride->setDateTime($dateTime);

        return $ride;
    }
}
