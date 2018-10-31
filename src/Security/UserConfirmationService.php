<?php

namespace App\Security;

use App\Entity\User;
use App\Exception\InvalidConfirmationTokenException;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;

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

    /** @var LoggerInterface $logger */
    private $logger;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $manager,
        LoggerInterface $logger
    )
    {
        $this->userRepository = $userRepository;
        $this->manager = $manager;
        $this->logger = $logger;
    }

    /**
     * @param string $confirmationToken
     *
     * @throws InvalidConfirmationTokenException
     */
    public function confirmUser(string $confirmationToken)
    {
        $this->logger->debug('Finding user by confirmationToken');
        /** @var User $user */
        $user = $this->userRepository->findOneBy(
            ['confirmationToken' => $confirmationToken]
        );

        if(!$user) {
            $this->logger->debug('User by confirmationToken not found.');
            throw new InvalidConfirmationTokenException();
        }

        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->manager->flush();
    }
}