<?php declare(strict_types=1);

namespace App\Controller;

use App\CycleFetcher\CycleFetcherInterface;
use App\ExecuteGenerator\CycleExecutable;
use App\RideGenerator\CycleRideGeneratorInterface;
use Carbon\CarbonInterval;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use OpenApi\Annotations as OA;
use Nelmio\ApiDocBundle\Annotation\Model;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/preview", name="api_preview", methods={"POST"})
     * @OA\Response(
     *     response=200,
     *     description="Returns a preview of the rides to be created by the provided CycleExecuteable.",
     *     @OA\Schema(
     *         type="array",
     *         @OA\Items(ref=@Model(type=App\Model\Ride::class, groups={"full"}))
     *     )
     * )
     * @OA\Parameter(
     *     name="body",
     *     in="body",
     *     description="The CycleExecuteable to create rides.",
     *     @OA\Schema(
     *         type="object",
     *         @OA\Items(ref=@Model(type=CycleExecuteable::class, groups={"full"}))
     *     )
     * )
     * @OA\Tag(name="Ride Generator")
     */
    public function preview(Request $request, SerializerInterface $serializer, CycleFetcherInterface $cycleFetcher, CycleRideGeneratorInterface $rideGenerator): Response
    {
        /** @var CycleExecutable $cycleExecuteable */
        $cycleExecuteable = $serializer->deserialize($request->getContent(), CycleExecutable::class, 'json');

        if ($cycleExecuteable->getCitySlug() && !$cycleExecuteable->getCity() && !$cycleExecuteable->getCityCycle()) {
            $cycleList = $cycleFetcher->fetchCycles([$cycleExecuteable->getCitySlug()]);
        } elseif ($cycleExecuteable->getCityCycle()) {
            $cycleList = [$cycleExecuteable->getCityCycle()];
        } else {
            $cycleList = [];
        }

        $rideGenerator->setCycleList($cycleList);

        $fromDateTime = $cycleExecuteable->getFromDate();
        $untilDateTime = $cycleExecuteable->getUntilDate();

        if ($fromDateTime && $untilDateTime) {
            $monthInterval = new CarbonInterval('P1M');

            do {
                $rideGenerator->addDateTime(clone $fromDateTime); // CarbonImmuteable should be used here

                $fromDateTime->add($monthInterval);
            } while ($fromDateTime <= $untilDateTime);
        }

        $rideList = $rideGenerator
            ->execute()
            ->getRideList();

        return new Response($serializer->serialize($rideList, 'json'));
    }
}
