<?php
declare(strict_types=1);

namespace App\Repository;

use App\Entity\Channel;
use App\Exception\Repository\CouldNotModifyChannelException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\ORMException;
use Symfony\Bridge\Doctrine\RegistryInterface;

class ChannelRepository extends ServiceEntityRepository
{
    /**
     * UserRepository constructor.
     * @param RegistryInterface $registry
     */
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Channel::class);
    }

    /**
     * @param Channel $channel
     * @throws CouldNotModifyChannelException
     */
    public function save(Channel $channel): void
    {
        try {
            $this->getEntityManager()->persist($channel);
            $this->getEntityManager()->flush();
        } catch (ORMException $exception) {
            throw CouldNotModifyChannelException::forError($exception);
        }
    }

    /**
     * @param Channel $channel
     * @throws CouldNotModifyChannelException
     */
    public function remove(Channel $channel): void
    {
        try {
            $this->getEntityManager()->remove($channel);
            $this->getEntityManager()->flush();
        } catch (ORMException $exception) {
            throw CouldNotModifyChannelException::forError($exception);
        }
    }

    /**
     * @param string $channel
     * @return Channel|null|object
     */
    public function findByName(string $channel): ?Channel
    {
        return $this->findOneBy(['channelName' => $channel]);
    }
}
