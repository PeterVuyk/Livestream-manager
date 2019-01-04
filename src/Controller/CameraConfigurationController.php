<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Configuration;
use App\Exception\CouldNotModifyCameraConfigurationException;
use App\Form\ConfigurationType;
use App\Service\CameraConfigurationService;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Flash\FlashBagInterface;
use Symfony\Component\Routing\RouterInterface;

class CameraConfigurationController extends Controller
{
    /** @var CameraConfigurationService */
    private $cameraConfigurationService;

    /** @var FormFactoryInterface */
    private $formFactory;

    /** @var RouterInterface */
    private $router;

    /** @var FlashBagInterface */
    private $flashBag;

    /**
     * CameraConfigurationController constructor.
     * @param \Twig_Environment $twig
     * @param CameraConfigurationService $cameraConfigurationService
     * @param FormFactoryInterface $formFactory
     * @param RouterInterface $router
     * @param FlashBagInterface $flashBag
     */
    public function __construct(
        \Twig_Environment $twig,
        CameraConfigurationService $cameraConfigurationService,
        FormFactoryInterface $formFactory,
        RouterInterface $router,
        FlashBagInterface $flashBag
    ) {
        $this->cameraConfigurationService = $cameraConfigurationService;
        $this->formFactory = $formFactory;
        $this->router = $router;
        $this->flashBag = $flashBag;
        parent::__construct($twig);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function configurationList(Request $request)
    {
        $cameraConfigurations = $this->cameraConfigurationService->getAllConfigurations();

        $configuration = new Configuration($cameraConfigurations);
        $form = $this->formFactory->create(ConfigurationType::class, $configuration);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->cameraConfigurationService->saveConfigurations($form->getData());
                $this->flashBag->add(self::SUCCESS_MESSAGE, 'flash.camera_configuration.success.updated');
            } catch (CouldNotModifyCameraConfigurationException $exception) {
                $this->flashBag->add(self::ERROR_MESSAGE, 'flash.camera_configuration.error.could_not_update');
            }
            return new RedirectResponse($this->router->generate('camera_configuration_list'));
        }

        return $this->render('configuration/list.html.twig', ['form' => $form->createView()]);
    }
}
