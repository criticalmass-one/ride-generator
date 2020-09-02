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
            'verify' => false,
        ]);

        $this->serializer = $serializer;
    }

    public function fetchCycles(array $citySlugList = []): array
    {
        if (0 === count($citySlugList)) {
            $parameters = [
                'validNow' => true,
            ];

            return $this->executeFetch($parameters);
        }

        $cycleList = [];

        foreach ($citySlugList as $citySlug) {
            $parameters = [
                'validNow' => true,
                'citySlug' => $citySlug,
            ];

             $cycleList += $this->executeFetch($parameters);
        }

        return $cycleList;
    }

    protected function executeFetch(array $parameters): array
    {
        $query = sprintf('/api/cycles?%s', http_build_query($parameters));

        $result = $this->client->get($query);

        $jsonContent = $result->getBody()->getContents();

        $cycleList = $this->serializer->deserialize($jsonContent, 'array<App\Model\CityCycle>', 'json');

        return $cycleList;
    }
}