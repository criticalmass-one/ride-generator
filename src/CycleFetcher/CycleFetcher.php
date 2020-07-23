<?php declare(strict_types=1);

namespace App\CycleFetcher;

use GuzzleHttp\Client;
use JMS\Serializer\SerializerInterface;

class CycleFetcher implements CycleFetcherInterface
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

    public function fetchCycles(): array
    {
        $parameters = [
            'validNow' => true,
        ];

        $query = sprintf('/api/cycles?%s', http_build_query($parameters));

        $result = $this->client->get($query);

        $jsonContent = $result->getBody()->getContents();

        $profileList = $this->serializer->deserialize($jsonContent, 'array<App\Model\CityCycle>', 'json');

        return $profileList;
    }
}