<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Oro\Bundle\SyncBundle\Client\ConnectionChecker;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

/**
 * Checks WebSocket backend connection
 */
class WebSocketBackendCheck implements CheckInterface
{
    /** @var ConnectionChecker */
    protected $checkerBackend;

    /**
     * @param ConnectionChecker $checkerBackend
     */
    public function __construct(ConnectionChecker $checkerBackend)
    {
        $this->checkerBackend = $checkerBackend;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        if (!$this->checkerBackend->checkConnection()) {
            return new Failure('Not available');
        }

        return new Success();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Check if WebSocket backend connection can be established';
    }
}
