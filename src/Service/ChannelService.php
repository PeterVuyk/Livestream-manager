<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\Channel;
use App\Exception\Repository\CouldNotModifyChannelException;
use App\Repository\ChannelRepository;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class ChannelService
{
    /** @var ChannelRepository */
    private $channelRepository;

    /**
     * @param ChannelRepository $channelRepository
     */
    public function __construct(ChannelRepository $channelRepository)
    {
        $this->channelRepository = $channelRepository;
    }

    /**
     * @param string $channelName
     * @return Channel|null|object
     */
    public function getChannelByName(string $channelName): ?Channel
    {
        return $this->channelRepository->findOneBy(['channelName' => $channelName]);
    }

    /**
     * @param Channel $channel
     * @throws CouldNotModifyChannelException
     */
    public function createChannel(Channel $channel): void
    {
        $this->channelRepository->save($channel);
    }

    /**
     * @param string $channelName
     * @throws CouldNotModifyChannelException
     */
    public function removeChannelByName(string $channelName): void
    {
        $channel = $this->getChannelByName($channelName);
        if (!$channel instanceof Channel) {
            return;
        }
        $this->channelRepository->remove($channel);
    }

    /**
     * @param Channel $channel
     * @throws CouldNotModifyChannelException
     */
    public function updateChannel(Channel $channel): void
    {
        $this->channelRepository->save($channel);
    }
}
