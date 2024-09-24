<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Elastic\Transport\NodePool\Resurrect\ResurrectInterface;
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
    public function __construct(
        protected ClientFactory $clientFactory,
        protected ResurrectInterface $resurrect,
        protected string $engineName,
        protected array $engineParameters
    ) {
    }

    #[\Override]
    public function check(): ResultInterface
    {
        if ($this->isConfigured()) {
            $client = $this->clientFactory->create($this->engineParameters['client']);
            $node = $client->getTransport()->getNodePool()->nextNode();

            return $this->resurrect->ping($node) && $node->isAlive() ? new Success() : new Failure();
        }

        return new Skip('Elasticsearch connection is not configured. Check Skipped.');
    }

    protected function isConfigured(): bool
    {
        return ElasticsearchEngine::ENGINE_NAME === $this->engineName;
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'Check if Elasticsearch is available in case it is configured';
    }
}
