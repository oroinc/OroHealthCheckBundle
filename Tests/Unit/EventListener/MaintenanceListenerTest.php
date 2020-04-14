<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\EventListener;

use Lexik\Bundle\MaintenanceBundle\Listener\MaintenanceListener as LexikMaintenanceListener;
use Oro\Bundle\HealthCheckBundle\EventListener\MaintenanceListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

class MaintenanceListenerTest extends \PHPUnit\Framework\TestCase
{
    /** @var LexikMaintenanceListener|\PHPUnit\Framework\MockObject\MockObject */
    protected $lexikListener;

    /** @var MaintenanceListener */
    protected $listener;

    protected function setUp(): void
    {
        $this->lexikListener = $this->createMock(LexikMaintenanceListener::class);

        $this->listener = new MaintenanceListener($this->lexikListener, ['test_route']);
    }

    public function testOnKernelRequestAllowedRoute()
    {
        /** @var GetResponseEvent|\PHPUnit\Framework\MockObject\MockObject $event */
        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn(new Request([], [], ['_route' => 'test_route']));

        $this->lexikListener->expects($this->never())
            ->method('onKernelRequest');

        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestNotAllowedRoute()
    {
        /** @var GetResponseEvent|\PHPUnit\Framework\MockObject\MockObject $event */
        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn(new Request([], [], ['_route' => 'test_route1']));

        $this->lexikListener->expects($this->once())
            ->method('onKernelRequest')
            ->with($this->identicalTo($event));

        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestEmptyRequest()
    {
        /** @var GetResponseEvent|\PHPUnit\Framework\MockObject\MockObject $event */
        $event = $this->createMock(GetResponseEvent::class);
        $event->expects($this->once())
            ->method('getRequest')
            ->willReturn(new Request());

        $this->lexikListener->expects($this->once())
            ->method('onKernelRequest')
            ->with($this->identicalTo($event));

        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelResponse()
    {
        /** @var FilterResponseEvent $event */
        $event = $this->createMock(FilterResponseEvent::class);

        $this->lexikListener->expects($this->once())
            ->method('onKernelResponse')
            ->with($this->identicalTo($event));

        $this->listener->onKernelResponse($event);
    }
}
