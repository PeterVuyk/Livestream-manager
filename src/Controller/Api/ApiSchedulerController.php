<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Api\StreamScheduleDTO;
use App\Service\ManageScheduleService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;

class ApiSchedulerController
{
    /** @var LoggerInterface */
    private $logger;

    /** @var ManageScheduleService */
    private $manageScheduleService;

    /**
     * ApiScheduleController constructor.
     * @param LoggerInterface $logger
     * @param ManageScheduleService $manageScheduleService
     */
    public function __construct(LoggerInterface $logger, ManageScheduleService $manageScheduleService)
    {
        $this->logger = $logger;
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
        try {
            foreach ($this->manageScheduleService->getAllSchedules() as $schedule) {
                if ($schedule->isWrecked() || $schedule->getDisabled()) {
                    continue;
                }
                $streamScheduleDTO = StreamScheduleDTO::createFromStreamSchedule($schedule);
                $response[] = $streamScheduleDTO->getPayload();
            }
            return new JsonResponse($response, JsonResponse::HTTP_OK);
        } catch (\Exception $exception) {
            $this->logger->error('Could not retrieve scheduled streams via API', ['exception' => $exception]);
        }
        return new JsonResponse($response, JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
