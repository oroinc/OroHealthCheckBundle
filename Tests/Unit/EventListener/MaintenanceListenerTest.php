<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\EventListener;

use Lexik\Bundle\MaintenanceBundle\Listener\MaintenanceListener as LexikMaintenanceListener;
use Oro\Bundle\HealthCheckBundle\EventListener\MaintenanceListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceListenerTest extends \PHPUnit\Framework\TestCase
{
    private LexikMaintenanceListener|\PHPUnit\Framework\MockObject\MockObject $lexikListener;

    private MaintenanceListener $listener;

    protected function setUp(): void
    {
        $this->lexikListener = $this->createMock(LexikMaintenanceListener::class);

        $this->listener = new MaintenanceListener($this->lexikListener, ['test_route']);
    }

    public function testOnKernelRequestAllowedRoute(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $event->expects(self::once())
            ->method('getRequest')
            ->willReturn(new Request([], [], ['_route' => 'test_route']));

        $this->lexikListener->expects(self::never())
            ->method('onKernelRequest');

        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestNotAllowedRoute(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $event->expects(self::once())
            ->method('getRequest')
            ->willReturn(new Request([], [], ['_route' => 'test_route1']));

        $this->lexikListener->expects(self::once())
            ->method('onKernelRequest')
            ->with(self::identicalTo($event));

        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestEmptyRequest(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $event->expects(self::once())
            ->method('getRequest')
            ->willReturn(new Request());

        $this->lexikListener->expects(self::once())
            ->method('onKernelRequest')
            ->with(self::identicalTo($event));

        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelResponse(): void
    {
        $event = $this->createMock(ResponseEvent::class);

        $this->lexikListener->expects(self::once())
            ->method('onKernelResponse')
            ->with(self::identicalTo($event));

        $this->listener->onKernelResponse($event);
    }
}
