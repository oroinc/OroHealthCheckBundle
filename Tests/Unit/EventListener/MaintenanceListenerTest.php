<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\EventListener;

use Oro\Bundle\HealthCheckBundle\EventListener\MaintenanceListener;
use Oro\Bundle\MaintenanceBundle\EventListener\MaintenanceListener as BaseMaintenanceListener;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

class MaintenanceListenerTest extends \PHPUnit\Framework\TestCase
{
    private BaseMaintenanceListener|\PHPUnit\Framework\MockObject\MockObject $baseListener;

    private MaintenanceListener $listener;

    protected function setUp(): void
    {
        $this->baseListener = $this->createMock(BaseMaintenanceListener::class);

        $this->listener = new MaintenanceListener($this->baseListener, ['test_route']);
    }

    public function testOnKernelRequestAllowedRoute(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $event->expects(self::once())
            ->method('getRequest')
            ->willReturn(new Request([], [], ['_route' => 'test_route']));

        $this->baseListener->expects(self::never())
            ->method('onKernelRequest');

        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelRequestNotAllowedRoute(): void
    {
        $event = $this->createMock(RequestEvent::class);
        $event->expects(self::once())
            ->method('getRequest')
            ->willReturn(new Request([], [], ['_route' => 'test_route1']));

        $this->baseListener->expects(self::once())
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

        $this->baseListener->expects(self::once())
            ->method('onKernelRequest')
            ->with(self::identicalTo($event));

        $this->listener->onKernelRequest($event);
    }

    public function testOnKernelResponse(): void
    {
        $event = $this->createMock(ResponseEvent::class);

        $this->baseListener->expects(self::once())
            ->method('onKernelResponse')
            ->with(self::identicalTo($event));

        $this->listener->onKernelResponse($event);
    }
}
