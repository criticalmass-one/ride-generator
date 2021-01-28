<?php declare(strict_types=1);

namespace App\Api;

use App\Model\Ride;
use Doctrine\Common\Annotations\AnnotationReader;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;

class RideApi implements RideApiInterface
{
    protected Client $client;
    protected SerializerInterface $serializer;

    public function __construct(string $criticalmassHostname, SerializerInterface $serializer)
    {
        $this->client = new Client([
            'base_uri' => $criticalmassHostname,
            'verify' => false,
        ]);

        $this->serializer = $serializer;

        // @see https://github.com/symfony/symfony/issues/29161
        AnnotationReader::addGlobalIgnoredName('alias');
    }

    public function getRideListInMonth(\DateTime $dateTime): array
    {
        $uri = sprintf('/api/ride?year=%d&month=%d&size=500', $dateTime->format('Y'), $dateTime->format('m'));

        $response = $this->client->get($uri);

        $resultList = $this->serializer->deserialize($response->getBody()->getContents(), 'array<App\Model\Ride>', 'json');

        $rideList = [];

        /** @var Ride $ride */
        foreach ($resultList as $ride) {
            $rideList[$ride->getId()] = $ride;
        }

        return $rideList;
    }
}
