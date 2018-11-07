<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\SchedulerService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ScheduleLogBookController extends Controller
{
    /** @var SchedulerService */
    private $schedulerService;

    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /**
     * StreamLoggingController constructor.
     * @param \Twig_Environment $twig
     * @param SchedulerService $schedulerService
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        \Twig_Environment $twig,
        SchedulerService $schedulerService,
        RouterInterface $router,
        FlashBagInterface $flashBag
    ) {
        parent::__construct($twig);
        $this->schedulerService = $schedulerService;
        $this->router = $router;
        $this->flashBag = $flashBag;
    }

    /**
     * @param string $scheduleId
     * @return Response|RedirectResponse
     */
    public function viewLogging(string $scheduleId)
    {
        try {
            $recurringSchedule = $this->schedulerService->getScheduleById($scheduleId);
        } catch (\Exception $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'Could not open latest logging from schedule.');
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render('scheduler/logging.html.twig', ['recurringSchedule' => $recurringSchedule]);
    }
}
