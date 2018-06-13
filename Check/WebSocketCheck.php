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
    protected $connectionChecker;

    /**
     * @param ConnectionChecker $connectionChecker
     */
    public function __construct(ConnectionChecker $connectionChecker)
    {
        $this->connectionChecker = $connectionChecker;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        if (!$this->connectionChecker->checkConnection()) {
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
