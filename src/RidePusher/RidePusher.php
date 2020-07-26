<?php declare(strict_types=1);

namespace App\RidePusher;

use App\Model\Ride;
use Doctrine\Common\Annotations\AnnotationReader;
use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;
use Symfony\Component\HttpFoundation\Response;

class RidePusher implements RidePusherInterface
{
    protected Client $client;
    protected SerializerInterface $serializer;

    public function __construct(SerializerInterface $serializer, string $criticalmassHostname)
    {
        $this->client = new Client([
            'base_uri' => $criticalmassHostname,
        ]);

        $this->serializer = $serializer;

        // @see https://github.com/symfony/symfony/issues/29161
        AnnotationReader::addGlobalIgnoredName('alias');
    }

    public function pushRide(Ride $ride): bool
    {
        if ($ride->getSlug()) {
            $rideIdentifier = $ride->getSlug();
        } else {
            $rideIdentifier = $ride->getDateTime()->format('Y-m-d');
        }

        $citySlug = $ride->getCity()->getSlugs()[0]->getSlug();

        $apiUrl = sprintf('/api/%s/%s', $citySlug, $rideIdentifier);

        try {
            $response = $this->client->put($apiUrl, [
                'body' => $this->serializer->serialize($ride, 'json'),
            ]);
        } catch (\Exception $e) {
            return false;
        }

        return Response::HTTP_CREATED === $response->getStatusCode();
    }

    public function pushRides(array $rideList): int
    {
        $successCounter = 0;

        foreach ($rideList as $ride) {
            $success = $this->pushRide($ride);

            if ($success) {
                ++$successCounter;
            }
        }

        return $successCounter;
    }
}
