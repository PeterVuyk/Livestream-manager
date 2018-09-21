<?php
declare(strict_types=1);

namespace App\Service;

use App\Entity\User;
use App\Repository\UserRepository;
use Doctrine\ORM\OptimisticLockException;
use Doctrine\ORM\ORMException;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    /** @var UserPasswordEncoderInterface */
    private $passwordEncoder;

    /** @var UserRepository */
    private $userRepository;

    /**
     * UserService constructor.
     * @param UserPasswordEncoderInterface $passwordEncoder
     * @param UserRepository $userRepository
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder, UserRepository $userRepository)
    {
        $this->passwordEncoder = $passwordEncoder;
        $this->userRepository = $userRepository;
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function createUser(User $user)
    {
        $password = $this->passwordEncoder->encodePassword($user, $user->getPlainPassword());
        $user->setPassword($password);
        $this->userRepository->save($user);
    }

    /**
     * @param User $user
     * @throws ORMException
     * @throws OptimisticLockException
     */
    public function updateUser(User $user)
    {
        $this->userRepository->save($user);
    }

    /**
     * @param int $userId
     * @return null|object
     */
    public function getUserById(int $userId)
    {
        return $this->userRepository->findOneBy(['id' => $userId]);
    }

    /**
     * @return User[]
     */
    public function getAllUsers()
    {
        return $this->userRepository->findAll();
    }
}
