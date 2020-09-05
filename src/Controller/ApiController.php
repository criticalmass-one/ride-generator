<?php declare(strict_types=1);

namespace App\Controller;

use App\ExecuteGenerator\CycleExecutable;
use JMS\Serializer\SerializerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

class ApiController extends AbstractController
{
    /**
     * @Route("/api/preview", name="api_preview", methods={"POST"})
     * @SWG\Response(
     *     response=200,
     *     description="Returns a preview of the rides to be created by the provided CycleExecuteable.",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=App\Model\Ride::class, groups={"full"}))
     *     )
     * )
     * @SWG\Parameter(
     *     name="body",
     *     in="body",
     *     type="string",
     *     description="The CycleExecuteable to create rides.",
     *     @SWG\Schema(
     *         type="object",
     *         @SWG\Items(ref=@Model(type=CycleExecuteable::class, groups={"full"}))
     *     )
     * )
     * @SWG\Tag(name="Ride Generator")
     */
    public function preview(Request $request, SerializerInterface $serializer): Response
    {
        $cycleExecuteable = $serializer->deserialize($request->getContent(), CycleExecutable::class, 'json');

        return $this->json($serializer->serialize($cycleExecuteable, 'json'));
    }
}
