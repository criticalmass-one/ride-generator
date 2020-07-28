<?php declare(strict_types=1);

namespace App\RidePusher;

use App\Model\Api\ApiResultInterface;
use App\Model\Api\ErrorResult;
use App\Model\Api\SuccessResult;
use App\Model\Ride;
use Doctrine\Common\Annotations\AnnotationReader;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use JMS\Serializer\SerializerInterface;
use Psr\Http\Message\ResponseInterface;

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

    public function pushRide(Ride $ride): ApiResultInterface
    {
        if ($ride->getSlug()) {
            $rideIdentifier = $ride->getSlug();
        } else {
            $rideIdentifier = $ride->getDateTime()->format('Y-m-d');
        }

        $citySlug = $ride->getCity()->getSlugs()[0]->getSlug();

        $apiUrl = sprintf('/api/%s/%s', $citySlug, $rideIdentifier);

        try {
            /** @var ResponseInterface $response */
             $this->client->put($apiUrl, [
                'body' => $this->serializer->serialize($ride, 'json'),
            ]);
        } catch (BadResponseException $e) {
            $responseBody = $e->getResponse()->getBody()->getContents();

            try {
                $errorResult = $this->serializer->deserialize($responseBody, ErrorResult::class, 'json');
            } catch (\Exception $exception) {
                $errorResult = new ErrorResult(500, [$exception->getMessage()]);
            }

            $errorResult->setRide($ride);

            return $errorResult;
        }

        return new SuccessResult($ride);
    }

    public function pushRides(array $rideList): array
    {
        $resultList = [];

        foreach ($rideList as $key => $ride) {
            $result = $this->pushRide($ride);

            $resultList[$key] = $result;
        }

        return $resultList;
    }
}
