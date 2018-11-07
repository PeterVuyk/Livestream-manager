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
    public function createStream(Request $request)
    {
        $form = $this->formFactory->create(StreamScheduleType::class, new StreamSchedule());
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->schedulerService->saveStream($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'Command successful added.');
            } catch (\Exception $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'Could not save schedule.');
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
        $streamSchedule = $this->schedulerService->getScheduleById($scheduleId);
        if (!$streamSchedule instanceof StreamSchedule) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'Can not edit requested schedule.');
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        $form = $this->formFactory->create(StreamScheduleType::class, $streamSchedule);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->schedulerService->saveStream($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'Command successful updated.');
            } catch (\Exception $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'Could not edit schedule.');
            }
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render(
            'scheduler/addStream.html.twig',
            array('form' => $form->createView())
        );
    }
}
