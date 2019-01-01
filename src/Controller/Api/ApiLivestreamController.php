<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\StatusStreamService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;

class ApiLivestreamController
{
    /** @var StatusStreamService */
    private $statusStreamService;

    /** @var LoggerInterface */
    private $logger;

    /**
     * ApiLivestreamController constructor.
     * @param StatusStreamService $statusStreamService
     * @param LoggerInterface $logger
     */
    public function __construct(StatusStreamService $statusStreamService, LoggerInterface $logger)
    {
        $this->statusStreamService = $statusStreamService;
        $this->logger = $logger;
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Get the livestream status"
     * )
     * @SWG\Tag(name="Livestream")
     * @return JsonResponse
     */
    public function getStatusLivestream()
    {
        try {
            $isRunning = $this->statusStreamService->isRunning();
            return new JsonResponse(['statusStream' => $isRunning], JsonResponse::HTTP_OK);
        } catch (\Exception $exception) {
            $this->logger->error('Could not retrieve status stream via API', ['exception' => $exception->getMessage()]);
        }
        return new JsonResponse(['failed collecting '], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
