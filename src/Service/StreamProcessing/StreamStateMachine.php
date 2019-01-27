<?php
declare(strict_types=1);

namespace App\Service\StreamProcessing;

use App\Entity\StateAwareInterface;
use App\Exception\CouldNotModifyCameraException;
use App\Repository\CameraRepository;
use App\Service\StateMachineInterface;
use Symfony\Component\Workflow\Registry;

class StreamStateMachine implements StateMachineInterface
{
    /** @var CameraRepository */
    private $cameraRepository;

    /** @var Registry */
    private $workflows;

    /**
     * @param CameraRepository $cameraRepository
     * @param Registry $workflows
     */
    public function __construct(CameraRepository $cameraRepository, Registry $workflows)
    {
        $this->cameraRepository = $cameraRepository;
        $this->workflows = $workflows;
    }

    public function can(StateAwareInterface $camera, string $transition): bool
    {
        $cameraWorkflow = $this->workflows->get($camera, 'camera_stream');
        return $cameraWorkflow->can($camera, $transition);
    }

    /**
     * @param StateAwareInterface $camera
     * @param string $transition
     * @throws CouldNotModifyCameraException
     */
    public function apply(StateAwareInterface $camera, string $transition): void
    {
        if (!$this->can($camera, $transition)) {
            throw new \InvalidArgumentException(
                sprintf('Invalid transition "%s" when in state "%s".', $transition, $camera->getState())
            );
        }

        $camera->setState($transition);
        $this->cameraRepository->save($camera);
    }
}
