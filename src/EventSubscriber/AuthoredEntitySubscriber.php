<?php

namespace App\EventSubscriber;

use App\Entity\AuthoredEntityInterface;
use App\Entity\User;
use ApiPlatform\Core\EventListener\EventPriorities;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Class AuthoredEntitySubscriber
 */
class AuthoredEntitySubscriber implements EventSubscriberInterface
{
    /** @var TokenStorageInterface $tokenStorage */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * Returns an array of events this subscriber wants to listen to.
     *
     * @return string[]
     */
    public static function getSubscribedEvents()
    {
        return [
          KernelEvents::VIEW => ['getAuthenticatedUser', EventPriorities::PRE_WRITE]
        ];
    }

    public function getAuthenticatedUser(GetResponseForControllerResultEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        /** @var User$author */
        $author = $this->tokenStorage->getToken()->getUser();

        if(!$entity instanceof AuthoredEntityInterface
            || Request::METHOD_POST !== $method) {
            return;
        }

        $entity->setAuthor($author);
    }
}