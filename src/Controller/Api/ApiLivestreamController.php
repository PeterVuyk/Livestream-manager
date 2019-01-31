<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\CouldNotFindMainCameraException;
use App\Service\LivestreamService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;

class ApiLivestreamController
{
    /** @var LivestreamService */
    private $livestreamService;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ApiLivestreamController constructor.
     * @param LivestreamService $livestreamService
     * @param LoggerInterface $logger
     */
    public function __construct(LivestreamService $livestreamService, LoggerInterface $logger)
    {
        $this->livestreamService = $livestreamService;
        $this->logger = $logger;
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Get the status livestream",
     *     @SWG\Schema(
     *             @SWG\Property(
     *     property="status",
     *     type="string",
     *     enum={"inactive", "starting", "running", "stopping", "failure"}
     *     ),
     *     )
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Failed getting the status, Internal server error",
     *     @SWG\Schema(
     *             @SWG\Property(property="message", type="string"),
     *     )
     * )
     * @SWG\Tag(name="Livestream")
     * @return JsonResponse
     */
    public function getStatusLivestream()
    {
        try {
            $camera = $this->livestreamService->getMainCameraStatus();
            return new JsonResponse(['status' => $camera->getState()], JsonResponse::HTTP_OK);
        } catch (CouldNotFindMainCameraException $exception) {
            $this->logger->error('Could not retrieve status stream via API', ['exception' => $exception->getMessage()]);
        }
        return new JsonResponse(['message' => 'something went wrong'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
