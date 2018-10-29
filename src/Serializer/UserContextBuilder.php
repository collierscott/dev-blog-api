<?php

namespace App\Serializer;

use ApiPlatform\Core\Serializer\SerializerContextBuilderInterface;
use App\Entity\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Class UserContextBuilder
 */
class UserContextBuilder implements SerializerContextBuilderInterface
{
    /** @var SerializerContextBuilderInterface $decorated */
    private $decorated;

    /**
     * @var AuthorizationCheckerInterface $authorizationChecker
     */
    private $authorizationChecker;

    public function __construct(
        SerializerContextBuilderInterface $decorated,
        AuthorizationCheckerInterface $authorizationChecker)
    {
        $this->decorated = $decorated;
        $this->authorizationChecker = $authorizationChecker;
    }

    /**
     * Creates a serialization context from a Request.
     *
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param bool $normalization
     * @param array|null $extractedAttributes
     *
     * @return array
     */
    public function createFromRequest(
        Request $request,
        bool $normalization,
        array $extractedAttributes = null
    ): array {
        $context = $this->decorated->createFromRequest(
            $request, $normalization, $extractedAttributes
        );

        // Class being serialized/deserialized
        $resourceClass = $context['resource_class'] ?? null; // Default to null

        if(
            User::class === $resourceClass &&
            isset($context['groups']) &&
            $normalization === true &&
            $this->authorizationChecker->isGranted(User::ROLE_ADMIN)) {
                $context['groups'][] = 'get-admin';
        }

        return $context;
    }
}