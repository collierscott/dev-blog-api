<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class UserConfirmationClass
 */
class UserConfirmationService
{

    /**
     * @var \App\Repository\UserRepository
     */
    private $userRepository;

    /** @var EntityManagerInterface $manager */
    private $manager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $manager)
    {
        $this->userRepository = $userRepository;
        $this->manager = $manager;
    }

    /**
     * @param string $confirmationToken
     *
     * @throws InvalidConfirmationTokenException
     */
    public function confirmUser(string $confirmationToken)
    {
        /** @var User $user */
        $user = $this->userRepository->findOneBy(
            ['confirmationToken' => $confirmationToken]
        );

        if(!$user) {
            throw new InvalidConfirmationTokenException();
        }

        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->manager->flush();
    }
}