<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\SchedulerService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ManageSchedulerController extends Controller
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
     * @return Response
     */
    public function list()
    {
        $scheduledItems = $this->schedulerService->getAllScheduledItems();

        return $this->render(
            'scheduler/list.html.twig',
            ['scheduledItems' => $scheduledItems]
        );
    }

    /**
     * @param string $scheduleId
     * @return RedirectResponse
     */
    public function toggleDisablingSchedule(string $scheduleId)
    {
        try {
            $this->schedulerService->toggleDisablingSchedule($scheduleId);
        } catch (\Exception $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.manage_schedule.error.could_not_disable_status');
        }
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }

    /**
     * @param string $scheduleId
     * @return RedirectResponse
     */
    public function executeScheduleWithNextExecution(string $scheduleId)
    {
        try {
            $this->schedulerService->executeScheduleWithNextExecution($scheduleId);
        } catch (\Exception $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.manage_schedule.error.could_not_toggle_execution');
        }
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }

    /**
     * @param string $scheduleId
     * @return RedirectResponse
     */
    public function removeSchedule(string $scheduleId)
    {
        try {
            $this->schedulerService->removeSchedule($scheduleId);
        } catch (\Exception $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.manage_schedule.error.could_not_remove');
        }
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }

    /**
     * @param string $scheduleId
     * @return RedirectResponse
     */
    public function unwreckSchedule(string $scheduleId)
    {
        try {
            $this->schedulerService->unwreckSchedule($scheduleId);
        } catch (\Exception $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.manage_schedule.error.could_not_activate');
        }
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }
}
