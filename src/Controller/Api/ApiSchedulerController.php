<?php
declare(strict_types=1);

namespace App\Controller\Api;

use App\Entity\Api\StreamScheduleDTO;
use App\Exception\StreamSchedule\CouldNotCreateStreamScheduleDTOException;
use App\Service\ManageScheduleService;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Swagger\Annotations as SWG;
use Nelmio\ApiDocBundle\Annotation\Model;

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
     *     description="Returns the scheduled livestreams",
     *     @SWG\Schema(
     *         type="array",
     *         @SWG\Items(ref=@Model(type=StreamScheduleDTO::class))
     *     )
     * )
     * @SWG\Response(
     *     response=500,
     *     description="Failed getting the scheduled livestream, Internal server error",
     *     @SWG\Schema(
     *             @SWG\Property(property="message", type="string"),
     *     )
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
                $response[] = $streamScheduleDTO->grabPayload();
            }
            return new JsonResponse($response, JsonResponse::HTTP_OK);
        } catch (CouldNotCreateStreamScheduleDTOException $exception) {
            $this->logger->error('Could not retrieve scheduled streams via API', ['exception' => $exception]);
        }
        return new JsonResponse(['message' => 'something went wrong'], JsonResponse::HTTP_INTERNAL_SERVER_ERROR);
    }
}
