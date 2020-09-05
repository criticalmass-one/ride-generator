<?php declare(strict_types=1);

namespace App\RideCalculator;

use App\Model\CityCycle;
use App\Model\Ride;
use App\RideNamer\GermanCityDateRideNamer;
use App\Validator\RideDateTimeValidator;
use Carbon\Carbon;
use Carbon\CarbonTimeZone;

class RideCalculator extends AbstractRideCalculator
{
    public function execute(): ?Ride
    {
        if ($this->cycle->getDayOfWeek() === null || $this->cycle->getWeekOfMonth() === null) {
            return null;
        }

        $dateTime = Carbon::create($this->year, $this->month);

        $cityTimeZone = new CarbonTimeZone($this->cycle->getCity()->getTimezone());
        $rideDateTime = $dateTime->setTimezone($cityTimeZone);

        $ride = $this->createRide($this->cycle, $rideDateTime);

        $constraintValidatonList = $this->validator->validate($ride);

        if (0 === count($constraintValidatonList)) {
            return $ride;
        }

        return null;
    }

    protected function createRide(CityCycle $cycle, Carbon $dateTime): Ride
    {
        $ride = new Ride();
        $ride
            ->setCity($cycle->getCity())
            ->setCycle($cycle);

        $ride = $this->calculateDate($cycle, $ride, $dateTime);
        $ride = $this->calculateTime($cycle, $ride);
        $ride = $this->setupLocation($cycle, $ride);
        $ride = $this->generateTitle($cycle, $ride);

        return $ride;
    }

    protected function calculateDate(CityCycle $cityCycle, Ride $ride, Carbon $startDateTime): Ride
    {
        $dayInterval = new \DateInterval('P1D');
        $weekInterval = new \DateInterval('P7D');

        $dateTime = clone $startDateTime;

        while ($dateTime->format('w') != $cityCycle->getDayOfWeek()) {
            $dateTime->add($dayInterval);
        }

        if ($cityCycle->getWeekOfMonth() > 0) {
            $weekOfMonth = $cityCycle->getWeekOfMonth();

            for ($i = 1; $i < $weekOfMonth; ++$i) {
                $dateTime->add($weekInterval);
            }
        } else {
            while ($dateTime->format('m') == $startDateTime->format('m')) {
                $dateTime->add($weekInterval);
            }

            $dateTime->sub($weekInterval);
        }

        $ride->setDateTime($dateTime);

        return $ride;
    }

    protected function calculateTime(CityCycle $cityCycle, Ride $ride): Ride
    {
        $time = $cityCycle->getTime();

        $intervalSpec = sprintf('PT%dH%dM', $time->format('H'), $time->format('i'));
        $timeInterval = new \DateInterval($intervalSpec);

        $dateTime = $ride->getDateTime();
        $dateTime->add($timeInterval);
        $ride->setDateTime($dateTime);

        return $ride;
    }

    protected function getCityTimeZone(CityCycle $cityCycle): \DateTimeZone
    {
        if ($timezoneSpec = $cityCycle->getCity()->getTimezone()) {
            $timezone = new \DateTimeZone($timezoneSpec);
        } else {
            $timezone = new \DateTimeZone('Europe/Berlin');
        }

        return $timezone;
    }

    protected function setupLocation(CityCycle $cityCycle, Ride $ride): Ride
    {
        $ride
            ->setLatitude($cityCycle->getLatitude())
            ->setLongitude($cityCycle->getLongitude())
            ->setLocation($cityCycle->getLocation());

        return $ride;
    }

    protected function generateTitle(CityCycle $cityCycle, Ride $ride): Ride
    {
        if (!$ride->getDateTime()) {
            return $ride;
        }

        if (!$cityCycle->getCity()->getRideNamer()) {
            $rideNamer = new GermanCityDateRideNamer();
        } else {
            $rideNamer = $this->rideNamerList->getRideNamerByFqcn($cityCycle->getCity()->getRideNamer());
        }

        $title = $rideNamer->generateTitle($ride);

        $ride->setTitle($title);

        return $ride;
    }
}
