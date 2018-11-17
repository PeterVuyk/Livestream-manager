<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\StreamSchedule;
use App\Form\CreateOnetimeScheduleType;
use App\Form\CreateRecurringScheduleType;
use App\Service\ManageScheduleService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class ScheduleController extends Controller
{
    /** @var ManageScheduleService */
    private $manageScheduleService;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /**
     * SchedulerController constructor.
     * @param ManageScheduleService $manageScheduleService
     * @param \Twig_Environment $twig
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        ManageScheduleService $manageScheduleService,
        \Twig_Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        FlashBagInterface $flashBag
    ) {
        parent::__construct($twig);
        $this->manageScheduleService = $manageScheduleService;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->flashBag = $flashBag;
    }

    /**
     * @return Response
     */
    public function list()
    {
        $onetimeScheduledItems = $this->manageScheduleService->getOnetimeSchedules();
        $recurringScheduledItems = $this->manageScheduleService->getRecurringSchedules();

        return $this->render(
            'scheduler/list/list.html.twig',
            ['recurringScheduledItems' => $recurringScheduledItems, 'onetimeScheduledItems' => $onetimeScheduledItems]
        );
    }

    /**
     * @todo: this and onetime schedule looks the same. make one function...
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createRecurringSchedule(Request $request)
    {

        $form = $this->formFactory->create(CreateRecurringScheduleType::class, new StreamSchedule());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->manageScheduleService->saveSchedule($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'flash.schedule.success.schedule_created');
            } catch (\Exception $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'flash.schedule.error.could_not_save_schedule');
            }
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render(
            'scheduler/createSchedule.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createOnetimeSchedule(Request $request)
    {
        $form = $this->formFactory->create(CreateOnetimeScheduleType::class, new StreamSchedule());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->manageScheduleService->saveSchedule($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'flash.schedule.success.schedule_created');
            } catch (\Exception $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'flash.schedule.error.could_not_save_schedule');
            }
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render(
            'scheduler/createSchedule.html.twig',
            array('form' => $form->createView())
        );
    }
}
