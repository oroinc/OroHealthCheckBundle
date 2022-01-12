<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\ElasticSearchBundle\Engine\ElasticSearch as ElasticsearchEngine;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class ElasticsearchCheckTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        if (!$this->isSupported()) {
            $this->markTestSkipped('ElasticSearch engine is not configured.');
        }
    }

    public function testExecuteApiCall()
    {
        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'elasticsearch'])
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }

    /**
     * @return bool
     */
    private function isSupported()
    {
        $engineName = $this->getContainer()
            ->get('oro_search.engine.parameters')
            ->getEngineName();

        return ElasticsearchEngine::ENGINE_NAME === $engineName;
    }
}
