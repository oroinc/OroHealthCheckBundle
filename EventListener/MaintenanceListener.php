<?php

namespace Oro\Bundle\HealthCheckBundle\EventListener;

use Lexik\Bundle\MaintenanceBundle\Listener\MaintenanceListener as LexikMaintenanceListener;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * Allows to work with service routes in maintenance mode
 */
class MaintenanceListener
{
    /** @var LexikMaintenanceListener */
    protected $listenerInner;

    /** @var array */
    protected $allowedRoutes = [];

    public function __construct(LexikMaintenanceListener $listenerInner, array $allowedRoutes)
    {
        $this->listenerInner = $listenerInner;
        $this->allowedRoutes = $allowedRoutes;
    }

    public function onKernelRequest(GetResponseEvent $event)
    {
        $request = $event->getRequest();
        if (!in_array($request->get('_route'), $this->allowedRoutes, true)) {
            $this->listenerInner->onKernelRequest($event);
        }
    }

    public function onKernelResponse(FilterResponseEvent $event)
    {
        $this->listenerInner->onKernelResponse($event);
    }
}
