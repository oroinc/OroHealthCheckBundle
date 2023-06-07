<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\SyncBundle\Client\ConnectionChecker;

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
            return $this->checker->isConfigured() && $this->strict
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
