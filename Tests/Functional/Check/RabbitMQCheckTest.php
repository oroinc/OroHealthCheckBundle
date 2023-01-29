<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RabbitMQCheckTest extends WebTestCase
{
    private const CONFIG_PROVIDER_SERVICE = 'oro_message_queue.transport.amqp.connection.config_provider';

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
        $rabbitMqCheck = self::getContainer()->get('oro_health_check.check.rabbitmq');

        $this->assertInstanceOf($this->getExpectedResult(), $rabbitMqCheck->check());
    }

    private function getExpectedResult(): string
    {
        $c = self::getContainer()->get(self::CONFIG_PROVIDER_SERVICE)?->getConfiguration();

        $result = Skip::class;
        if (is_array($c) && isset($c['host'], $c['port'], $c['user'], $c['password'], $c['vhost'])) {
            $result = Success::class;
        }

        return $result;
    }
}
