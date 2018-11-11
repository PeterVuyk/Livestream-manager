<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\RecurringSchedule;
use App\Form\RecurringScheduleType;
use App\Service\SchedulerService;
use Doctrine\DBAL\Types\Type;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class SetupStreamController extends Controller
{
    /** @var SchedulerService */
    private $schedulerService;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /**
     * SchedulerController constructor.
     * @param SchedulerService $schedulerService
     * @param \Twig_Environment $twig
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        SchedulerService $schedulerService,
        \Twig_Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        FlashBagInterface $flashBag
    ) {
        parent::__construct($twig);
        $this->schedulerService = $schedulerService;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->flashBag = $flashBag;
    }

    /**
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createRecurringStream(Request $request)
    {
        $form = $this->formFactory->create(RecurringScheduleType::class, new RecurringSchedule());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->schedulerService->saveStream($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'flash.setup_stream.success.schedule_created');
            } catch (\Exception $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'flash.setup_stream.error.could_not_save_schedule');
            }
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render(
            'scheduler/createRecurringStream.html.twig',
            array('form' => $form->createView())
        );
    }

    /**
     * @param string $scheduleId
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function editStream(string $scheduleId, Request $request)
    {
        $recurringSchedule = $this->schedulerService->getScheduleById($scheduleId);
        if (!$recurringSchedule instanceof RecurringSchedule) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.setup_stream.error.can_not_edit_schedule');
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        $form = $this->formFactory->create(RecurringScheduleType::class, $recurringSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->schedulerService->saveStream($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'flash.setup_stream.success.schedule_updated');
            } catch (\Exception $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'flash.setup_stream.error.can_not_edit_schedule');
            }
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render(
            'scheduler/createRecurringStream.html.twig',
            array('form' => $form->createView())
        );
    }
}
