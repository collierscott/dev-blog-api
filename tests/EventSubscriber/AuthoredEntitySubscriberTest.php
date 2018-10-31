<?php

namespace App\Tests\EventSubscriber;

use ApiPlatform\Core\EventListener\EventPriorities;
use App\EventSubscriber\AuthoredEntitySubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpKernel\KernelEvents;

/**
 * Class AuthoredEntitySubscriberTest
 */
class AuthoredEntitySubscriberTest extends TestCase
{
    public function testConfiguration()
    {
        $result = AuthoredEntitySubscriber::getSubscribedEvents();
        
        $this->assertArrayHasKey(KernelEvents::VIEW, $result);
        $this->assertEquals(
            ['getAuthenticatedUser', EventPriorities::PRE_WRITE],
            $result[KernelEvents::VIEW]
        );
    }
}