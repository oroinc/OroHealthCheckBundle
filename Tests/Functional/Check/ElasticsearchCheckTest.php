<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\ElasticSearchBundle\Engine\ElasticSearch as ElasticsearchEngine;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Result\Success;

class ElasticsearchCheckTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
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
    
    public function testServiceCheck()
    {
        $elasticSearchCheck = static::getContainer()->get('oro_health_check.check.elasticsearch');

        $this->assertInstanceOf(Success::class, $elasticSearchCheck->check());
    }

    /**
     * @return bool
     */
    private function isSupported()
    {
        return ElasticsearchEngine::ENGINE_NAME === static::getContainer()->getParameter('oro_search.engine');
    }
}
