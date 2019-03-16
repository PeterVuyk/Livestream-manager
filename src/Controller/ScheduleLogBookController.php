<?php
declare(strict_types=1);

namespace App\Controller;

use App\Service\ManageScheduleService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class ScheduleLogBookController extends Controller
{
    /** @var ManageScheduleService */
    private $manageScheduleService;

    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /**
     * StreamLoggingController constructor.
     * @param \Twig_Environment $twig
     * @param TokenStorageInterface $tokenStorage
     * @param ManageScheduleService $manageScheduleService
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        \Twig_Environment $twig,
        TokenStorageInterface $tokenStorage,
        ManageScheduleService $manageScheduleService,
        RouterInterface $router,
        FlashBagInterface $flashBag
    ) {
        parent::__construct($twig, $tokenStorage);
        $this->manageScheduleService = $manageScheduleService;
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
            $streamSchedule = $this->manageScheduleService->getScheduleById($scheduleId);
        } catch (\Exception $exception) {
            $this->flashBag->add(self::ERROR_MESSAGE, 'flash.logbook.error.could_not_open');
            return new RedirectResponse($this->router->generate('scheduler_list'));
        }
        return $this->render('scheduler/logging.html.twig', ['streamSchedule' => $streamSchedule]);
    }
}
