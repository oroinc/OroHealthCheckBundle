<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Oro\Bundle\SyncBundle\Client\ConnectionChecker;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Skip;
use ZendDiagnostics\Result\Success;

/**
 * Checks WebSocket connection
 */
class WebSocketCheck implements CheckInterface
{
    /** @var ConnectionChecker */
    protected $checker;

    /** @var string */
    protected $type;

    /** @var bool */
    protected $strict;

    /**
     * @param ConnectionChecker $checker
     * @param string $type
     * @param bool $strict
     */
    public function __construct(ConnectionChecker $checker, string $type, bool $strict = true)
    {
        $this->checker = $checker;
        $this->type = $type;
        $this->strict = $strict;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        if (!$this->checker->checkConnection()) {
            return $this->strict
                ? new Failure('Not available.')
                : new Skip('Not available. Skipped as this check is not mandatory.');
        }

        return new Success();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return sprintf('Check if WebSocket %s connection can be established', $this->type);
    }
}
