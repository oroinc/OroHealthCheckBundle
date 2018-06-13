<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\ElasticSearchBundle\Engine\ElasticSearch as ElasticsearchEngine;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Result\Skip;
use ZendDiagnostics\Result\Success;

class ElasticsearchCheckTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());
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

        $this->assertInstanceOf($this->getExpectedResult(), $elasticSearchCheck->check());
    }

    /**
     * @return string
     */
    private function getExpectedResult()
    {
        return ElasticsearchEngine::ENGINE_NAME === static::getContainer()->getParameter('oro_search.engine')
            ? Success::class
            : Skip::class;
    }
}
