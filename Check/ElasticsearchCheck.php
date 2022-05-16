<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\ElasticSearchBundle\Client\ClientFactory;
use Oro\Bundle\ElasticSearchBundle\Engine\ElasticSearch as ElasticsearchEngine;

/**
 * Class for check Elasticsearch availability
 */
class ElasticsearchCheck implements CheckInterface
{
    /** @var ClientFactory */
    protected $clientFactory;

    /** @var string */
    protected $engineName;

    /** @var array */
    protected $engineParameters;

    public function __construct(ClientFactory $clientFactory, string $engineName, array $engineParameters)
    {
        $this->clientFactory = $clientFactory;
        $this->engineName = $engineName;
        $this->engineParameters = $engineParameters;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        if ($this->isConfigured()) {
            $client = $this->clientFactory->create($this->engineParameters['client']);

            $connection = $client->getTransport()->getConnection();
            if (!$connection instanceof Connection) {
                return new Skip('Elasticsearch connection does not support ping. Check Skipped.');
            }

            return $connection->ping() && $connection->isAlive() ? new Success() : new Failure();
        }

        return new Skip('Elasticsearch connection is not configured. Check Skipped.');
    }

    protected function isConfigured(): bool
    {
        return ElasticsearchEngine::ENGINE_NAME === $this->engineName;
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Check if Elasticsearch is available in case it is configured';
    }
}
