<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Exception\FailedStartingLivestreamException;
use App\Exception\FailedStoppingLivestreamException;
use App\Service\LivestreamService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;

class LivestreamController
{
    /** @var LivestreamService */
    private $livestreamService;

    /**
     * LivestreamController constructor.
     * @param LivestreamService $livestreamService
     */
    public function __construct(LivestreamService $livestreamService)
    {
        $this->livestreamService = $livestreamService;
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Start livestream"
     * )
     * @return JsonResponse
     */
    public function startLivestream()
    {

        try {
            $this->livestreamService->startLivestream();
        } catch (FailedStartingLivestreamException $e) {
            return new JsonResponse('{"success": false}', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new JsonResponse('{"success": true}', JsonResponse::HTTP_CREATED);
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Stop livestream"
     * )
     * @return JsonResponse
     */
    public function stopLivestream()
    {
        try {
            $this->livestreamService->stopLivestream();
        } catch (FailedStoppingLivestreamException $e) {
            return new JsonResponse('{"success": false}', JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
        }
        return new JsonResponse('{"success": true}', JsonResponse::HTTP_CREATED);
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Get status livestream"
     * )
     * @return JsonResponse
     */
    public function getStatusLivestream()
    {
        $status = $this->livestreamService->isLivestreamRunning();
        return new JsonResponse(json_encode(["success" => $status]), JsonResponse::HTTP_OK);
    }
}
