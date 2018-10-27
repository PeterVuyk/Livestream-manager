<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;

class LivestreamController
{
    /**
     * @SWG\Response(
     *     response=200,
     *     description="Start livestream"
     * )
     * @return JsonResponse
     */
    public function startLivestream()
    {
        //TODO: Call to start livestream.
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
        //TODO: Call to stop livestream.
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
        //TODO: Call to get status livestream.
        return new JsonResponse(json_encode(["success" => '$status']), JsonResponse::HTTP_OK);
    }
}
