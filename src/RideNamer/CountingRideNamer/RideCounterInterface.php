<?php declare(strict_types=1);

namespace App\RideNamer\CountingRideNamer;

use App\Model\City;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;

interface RideCounterInterface
{
    public function countRides(City $city): int;
}
