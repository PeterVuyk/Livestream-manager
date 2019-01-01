<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Service\ManageScheduleService;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;

class ApiSchedulerController
{
    /** @var ManageScheduleService */
    private $manageScheduleService;

    /**
     * ApiScheduleController constructor.
     * @param ManageScheduleService $manageScheduleService
     */
    public function __construct(ManageScheduleService $manageScheduleService)
    {
        $this->manageScheduleService = $manageScheduleService;
    }

    /**
     * @SWG\Response(
     *     response=200,
     *     description="Get the stream schedule list"
     * )
     * @SWG\Tag(name="Scheduler")
     * @return JsonResponse
     */
    public function getStreamSchedule()
    {
        $response = [];
        foreach ($this->manageScheduleService->getAllSchedules() as $schedule) {
            $response[] = $schedule->getPayload();
        }
        return new JsonResponse($response);
    }
}
