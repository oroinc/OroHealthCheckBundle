<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Predis\Client as PredisClient;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

/**
 * Validate that a Redis service is running
 */
class RedisCheck implements CheckInterface
{
    /** @var PredisClient */
    protected $client;

    /** @var string */
    protected $type;

    /**
     * @param PredisClient $client
     * @param string $type
     */
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
        $this->client->ping();
        
        return new Success();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return sprintf('Check if %s is available', $this->type);
    }
}
