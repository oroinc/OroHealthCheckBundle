<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Oro\Bundle\SyncBundle\Client\ConnectionChecker;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

/**
 * Class for check WebSocket
 */
class WebSocketCheck implements CheckInterface
{
    /** @var ConnectionChecker */
    protected $checkerBackend;

    /** @var ConnectionChecker */
    protected $checkerFrontend;

    /**
     * @param ConnectionChecker $checkerBackend
     * @param ConnectionChecker $checkerFrontend
     */
    public function __construct(ConnectionChecker $checkerBackend, ConnectionChecker $checkerFrontend)
    {
        $this->checkerBackend = $checkerBackend;
        $this->checkerFrontend = $checkerFrontend;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        if (!$this->checkerBackend->checkConnection() || !$this->checkerFrontend->checkConnection()) {
            return new Failure('Not available');
        }

        return new Success();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Check if WebSocket server is available';
    }
}
