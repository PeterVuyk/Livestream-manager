<?php
declare(strict_types=1);

namespace App\Form;

use App\Entity\Channel;
use App\Repository\ChannelRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @SuppressWarnings(PHPMD.UnusedFormalParameter)
 */
class ChannelType extends AbstractType
{
    /** @var ChannelRepository */
    private $channelRepository;

    public function __construct(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(['choices' => $this->getChannels()]);
    }

    public function getParent(): string
    {
        return ChoiceType::class;
    }

    private function getChannels(): array
    {
        $items = ['Admin' => 'Admin'];
        /** @var Channel $channel */
        foreach ($this->channelRepository->findAll() as $channel) {
            $items[$channel->getName()] = $channel->getName();
        }
        return $items;
    }
}
