<?php

namespace Oro\Bundle\HealthCheckBundle\EventListener;

use Oro\Bundle\MaintenanceBundle\EventListener\MaintenanceListener as BaseMaintenanceListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Allows to work with service routes in maintenance mode
 */
class MaintenanceListener
{
    protected BaseMaintenanceListener $listenerInner;

    protected array $allowedRoutes = [];

    public function __construct(BaseMaintenanceListener $listenerInner, array $allowedRoutes)
    {
        $this->listenerInner = $listenerInner;
        $this->allowedRoutes = $allowedRoutes;
    }

    public function onKernelRequest(RequestEvent $event): void
    {
        $request = $event->getRequest();
        if (!in_array($request->get('_route'), $this->allowedRoutes, true)) {
            $this->listenerInner->onKernelRequest($event);
        }
    }

    public function onKernelResponse(ResponseEvent $event): void
    {
        $this->listenerInner->onKernelResponse($event);
    }
}
