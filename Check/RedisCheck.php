<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Predis\Client as PredisClient;

/**
 * Validate that a Redis service is running
 */
class RedisCheck implements CheckInterface
{
    /** @var PredisClient */
    protected $client;

    /** @var string */
    protected $type;

    public function __construct(PredisClient $client, string $type)
    {
        $this->client = $client;
        $this->type = $type;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        try {
            $ping = $this->client?->ping();
            $payload = $ping?->getPayload();
        } catch (\Throwable $throwable) {
            $payload = null;
        }

        if ($payload === 'PONG') {
            return new Success();
        } else {
            return new Failure();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return sprintf('Check if %s is available', $this->type);
    }
}
