<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\StreamSchedule;
use App\Form\StreamScheduleType;
use App\Service\SchedulerService;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\RouterInterface;

class RecurringSchedulerController extends Controller
{
    /** @var SchedulerService */
    private $schedulerService;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RouterInterface */
    private $router;

    /**
     * SchedulerController constructor.
     * @param SchedulerService $schedulerService
     * @param \Twig_Environment $twig
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     */
    public function __construct(
        SchedulerService $schedulerService,
        \Twig_Environment $twig,
        FormFactoryInterface $formFactory,
        RouterInterface $router
    ) {
        parent::__construct($twig);
        $this->schedulerService = $schedulerService;
        $this->formFactory = $formFactory;
        $this->router = $router;
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
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public function createStream(Request $request)
    {
        $form = $this->formFactory->create(StreamScheduleType::class, new StreamSchedule());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var Session $session */
            $session = $request->getSession();
            try {
                $this->schedulerService->saveStream($form->getData());
                $session->getFlashBag()->add(self::SUCCESS_MESSAGE, 'Command successful added.');
            } catch (\Exception $exception) {
                $session->getFlashBag()->add(self::ERROR_MESSAGE, 'Could not save schedule.');
            }
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render(
            'scheduler/addStream.html.twig',
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
        /** @var Session $session */
        $session = $request->getSession();
        $streamSchedule = $this->schedulerService->getScheduleById($scheduleId);
        if (!$streamSchedule instanceof StreamSchedule) {
            $session->getFlashBag()->add(self::ERROR_MESSAGE, 'Can not edit requested schedule.');
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        $form = $this->formFactory->create(StreamScheduleType::class, $streamSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->schedulerService->saveStream($form->getData());
                $session->getFlashBag()->add(self::SUCCESS_MESSAGE, 'Command successful updated.');
            } catch (\Exception $exception) {
                $session->getFlashBag()->add(self::ERROR_MESSAGE, 'Could not edit schedule.');
            }
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render(
            'scheduler/addStream.html.twig',
            array('form' => $form->createView())
        );
    }
}
