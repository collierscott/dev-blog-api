<?php

namespace App\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class UserConfirmationSubscriber
 */
class UserConfirmationSubscriber implements EventSubscriberInterface
{

    /** @var UserRepository  */
    private $userRepository;

    /** @var EntityManagerInterface */
    private $manaager;

    public function __construct(
        UserRepository $userRepository,
        EntityManagerInterface $manager
    )
    {
        $this->userRepository = $userRepository;
        $this->manaager = $manager;
    }

    /**
     * Returns an array of event names this subscriber wants to listen to.
     *
     * The array keys are event names and the value can be:
     *
     *  * The method name to call (priority defaults to 0)
     *  * An array composed of the method name to call and the priority
     *  * An array of arrays composed of the method names to call and respective
     *    priorities, or 0 if unset
     *
     * For instance:
     *
     *  * array('eventName' => 'methodName')
     *  * array('eventName' => array('methodName', $priority))
     *  * array('eventName' => array(array('methodName1', $priority), array('methodName2')))
     *
     * @return array The event names to listen to
     */
    public static function getSubscribedEvents()
    {
        return [
            KernelEvents::VIEW => ['confirmUser', EventPriorities::POST_VALIDATE]
        ];
    }

    public function confirmUser(GetResponseForControllerResultEvent $event)
    {
        $request = $event->getRequest();

        if('api_user_confirmations_post_collection' !== $request->get('_route')) {
            return;
        }

        $confirmationToken = $event->getControllerResult();

        /** @var \App\Entity\User $user */
        $user = $this->userRepository->findOneBy(
            ['confirmationToken' => $confirmationToken->confirmationToken]
        );

        if(!$user) {
            throw new NotFoundHttpException();
        }

        $user->setEnabled(true);
        $user->setConfirmationToken(null);
        $this->manaager->flush();

        $event->setResponse(new JsonResponse(
            null, Response::HTTP_OK
        ));
    }
}