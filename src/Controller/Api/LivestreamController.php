<?php
declare(strict_types=1);

namespace App\Controller\Api;

use Symfony\Component\HttpFoundation\JsonResponse;

class LivestreamController
{
    /**
     * @return JsonResponse
     */
    public function startLivestream()
    {
        return new JsonResponse('{"success": true}');
    }
}
