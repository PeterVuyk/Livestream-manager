<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\StreamSchedule;
use App\Form\UpdateScheduleType;
use App\Service\ManageScheduleService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ManageScheduleController extends Controller
{
    /** @var ManageScheduleService */
    private $manageScheduleService;

    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /** @var FormFactoryInterface */
    private $formFactory;

    /**
     * StreamLoggingController constructor.
     * @param \Twig_Environment $twig
     * @param ManageScheduleService $manageScheduleService
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     * @param FormFactoryInterface $formFactory
     */
    public function __construct(
        \Twig_Environment $twig,
        ManageScheduleService $manageScheduleService,
        RouterInterface $router,
        FlashBagInterface $flashBag,
        FormFactoryInterface $formFactory
    ) {
        parent::__construct($twig);
        $this->manageScheduleService = $manageScheduleService;
        $this->router = $router;
        $this->flashBag = $flashBag;
        $this->formFactory = $formFactory;
    }


    /**
     * @param string $scheduleId
     * @return RedirectResponse
     */
    public function toggleDisablingSchedule(string $scheduleId)
    {
        try {
            $schedule = $this->manageScheduleService->getScheduleById($scheduleId);
            $this->manageScheduleService->toggleDisablingSchedule($schedule);
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
            $schedule = $this->manageScheduleService->getScheduleById($scheduleId);
            $this->manageScheduleService->executeScheduleWithNextExecution($schedule);
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
            $schedule = $this->manageScheduleService->getScheduleById($scheduleId);
            $this->manageScheduleService->removeSchedule($schedule);
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
            $schedule = $this->manageScheduleService->getScheduleById($scheduleId);
            $this->manageScheduleService->unwreckSchedule($schedule);
        } catch (\Exception $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.manage_schedule.error.could_not_activate');
        }
        return new RedirectResponse($this->router->generate('scheduler_list'));
    }

    /**
     * @param string $scheduleId
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editSchedule(string $scheduleId, Request $request)
    {
        $streamSchedule = $this->manageScheduleService->getScheduleById($scheduleId);
        if (!$streamSchedule instanceof StreamSchedule) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.manage_schedule.error.can_not_edit_schedule');
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        $form = $this->formFactory->create(UpdateScheduleType::class, $streamSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->manageScheduleService->saveSchedule($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'flash.manage_schedule.success.schedule_updated');
            } catch (\Exception $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'flash.manage_schedule.error.can_not_edit_schedule');
            }
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render(
            'scheduler/editSchedule.html.twig',
            array('form' => $form->createView())
        );
    }
}
