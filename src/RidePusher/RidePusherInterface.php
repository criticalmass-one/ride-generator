<?php declare(strict_types=1);

namespace App\RidePusher;

use App\Model\Ride;

interface RidePusherInterface
{
    public function pushRide(Ride $ride): bool;
    public function pushRides(array $rideList): int;
}
