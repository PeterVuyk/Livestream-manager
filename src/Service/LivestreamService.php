<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Camera;
use App\Exception\CouldNotFindMainCameraException;
use App\Repository\CameraRepository;

class LivestreamService
{
    /** @var StateMachineInterface */
    private $streamStateMachine;

    /** @var CameraRepository */
    private $cameraRepository;

    /**
     * LivestreamService constructor.
     * @param StateMachineInterface $streamStateMachine
     * @param CameraRepository $cameraRepository
     */
    public function __construct(StateMachineInterface $streamStateMachine, CameraRepository $cameraRepository)
    {
        $this->streamStateMachine = $streamStateMachine;
        $this->cameraRepository = $cameraRepository;
    }

    /**
     * @return Camera
     * @throws CouldNotFindMainCameraException
     */
    public function getMainCameraStatus(): Camera
    {
        $camera = $this->cameraRepository->getMainCamera();
        if (!$camera instanceof Camera) {
            throw CouldNotFindMainCameraException::fromRepository();
        }
        return $camera;
    }
}
