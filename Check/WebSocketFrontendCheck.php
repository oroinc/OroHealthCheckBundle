<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Oro\Bundle\SyncBundle\Client\ConnectionChecker;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;

/**
 * Checks WebSocket frontend connection
 */
class WebSocketFrontendCheck implements CheckInterface
{
    /** @var ConnectionChecker */
    protected $checkerFrontend;

    /**
     * @param ConnectionChecker $checkerFrontend
     */
    public function __construct(ConnectionChecker $checkerFrontend)
    {
        $this->checkerFrontend = $checkerFrontend;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        if (!$this->checkerFrontend->checkConnection()) {
            return new Warning('Not available');
        }

        return new Success();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Check if WebSocket frontend connection can be established';
    }
}
