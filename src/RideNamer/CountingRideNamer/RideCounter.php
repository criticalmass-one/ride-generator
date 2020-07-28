<?php declare(strict_types=1);

namespace App\RideNamer\CountingRideNamer;

use App\Model\City;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;

class RideCounter implements RideCounterInterface
{
    protected Client $client;
    protected SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer, string $criticalmassHostname)
    {
        $this->client = new Client([
            'base_uri' => $criticalmassHostname,
        ]);

        $this->serializer = $serializer;
    }

    public function countRides(City $city): int
    {
        $uri = sprintf('/api/ride?citySlug=%s&size=10000', $city->getSlugs()[0]->getSlug());

        $response = $this->client->get($uri);

        $rideList = $this->serializer->deserialize($response->getBody()->getContents(), 'array<App\Model\Ride>', 'json');

        return count($rideList);
    }
}