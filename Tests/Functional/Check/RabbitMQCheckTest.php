<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Result\Skip;
use ZendDiagnostics\Result\Success;

class RabbitMQCheckTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }
    
    public function testExecuteApiCall()
    {
        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'rabbitmq_server'])
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }
    
    public function testServiceCheck()
    {
        $rabbitMqCheck = static::getContainer()->get('oro_health_check.check.rabbitmq');

        $this->assertInstanceOf($this->getExpectedResult(), $rabbitMqCheck->check());
    }

    /**
     * @return bool
     */
    protected function getExpectedResult()
    {
        $c = static::getContainer()->getParameter('message_queue_transport_config');

        $result = Skip::class;
        if (is_array($c) && isset($c['host'], $c['port'], $c['user'], $c['password'], $c['vhost'])) {
            $result = Success::class;
        }

        return $result;
    }
}
