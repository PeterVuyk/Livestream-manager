<?php
declare(strict_types=1);

namespace App\Twig;

use Twig\Extension\AbstractExtension;

class LivestreamStateMachineExtension extends AbstractExtension
{
    const STATE_MACHINE_CAN = 'stateMachineCan';

    /**
     * @return array
     */
    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction(
                self::STATE_MACHINE_CAN,
                [$this, self::STATE_MACHINE_CAN],
                ['is_safe' => ['html']]
            ),
        ];
    }

    /**
     * @param string $currentStatus
     * @param string $transition
     * @return bool
     */
    public function stateMachineCan(string $currentStatus, string $transition): bool
    {
        switch ($currentStatus) {
            case 'inactive':
                return ($transition === 'to_starting');
            case 'starting':
                return ($transition === 'to_running');
            case 'running':
                return ($transition === 'to_stopping');
            case 'stopping':
                return ($transition === 'to_inactive');
            default:
                return false;
        }
    }
}
