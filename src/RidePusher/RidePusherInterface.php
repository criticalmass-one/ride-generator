<?php declare(strict_types=1);

namespace App\RidePusher;

use App\Model\Api\ApiResultInterface;
use App\Model\Ride;

interface RidePusherInterface
{
    public function pushRide(Ride $ride): ApiResultInterface;
    public function pushRides(array $rideList): array;
}
