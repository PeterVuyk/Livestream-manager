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
     *     description="Returns response if livestream could be started"
     * )
     * @return JsonResponse
     */
    public function startLivestream()
    {
        return new JsonResponse('{"success": true}');
    }
}
