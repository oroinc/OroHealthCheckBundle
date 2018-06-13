<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Elasticsearch\Connections\Connection;
use Oro\Bundle\ElasticSearchBundle\Client\ClientFactory;
use Oro\Bundle\ElasticSearchBundle\Engine\ElasticSearch as ElasticsearchEngine;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Skip;

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
    
    /**
     * @param ClientFactory $clientFactory
     * @param string $engineName
     * @param array $engineParameters
     */
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

            $connection = $client->transport->getConnection();
            if (!$connection instanceof Connection) {
                return new Skip('Elasticsearch connection does not support ping. Check Skipped.');
            }

            return $connection->ping() && $connection->isAlive() ? new Success() : new Failure();
        }

        return new Skip('Elasticsearch connection is not configured. Check Skipped.');
    }
    
    /**
     * @return bool
     */
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
