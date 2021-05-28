<?php

namespace Oro\Bundle\HealthCheckBundle\EventListener;

use Lexik\Bundle\MaintenanceBundle\Listener\MaintenanceListener as LexikMaintenanceListener;
use Symfony\Component\HttpKernel\Event\RequestEvent;
use Symfony\Component\HttpKernel\Event\ResponseEvent;

/**
 * Allows to work with service routes in maintenance mode
 */
class MaintenanceListener
{
    protected LexikMaintenanceListener $listenerInner;

    protected array $allowedRoutes = [];

    public function __construct(LexikMaintenanceListener $listenerInner, array $allowedRoutes)
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
