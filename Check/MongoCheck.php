<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\GridFSConfigBundle\Provider\MongoDbDriverConfig;

/**
 * Checks the MongoDB connection for the ORO application.
 */
class MongoCheck implements CheckInterface
{
    public function __construct(private ?MongoDbDriverConfig $mongoDbDriverConfig)
    {
    }

    #[\Override]
    public function check(): ResultInterface
    {
        if (!$this->mongoDbDriverConfig) {
            return new Skip('MongoDB is not configured for this ORO application.');
        }

        if ($this->mongoDbDriverConfig->isConnected()) {
            return new Success();
        }

        return new Failure('Failed to connect to MongoDb');
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'Checks MongoDB connectivity if applicable';
    }
}
