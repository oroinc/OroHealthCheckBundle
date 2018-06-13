<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Result\Success;

class WebSocketCheckTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->markTestSkipped('CI doen\'t run clank server.');

        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testExecuteApiCall()
    {
        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'websocket'])
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }

    public function testServiceCheck()
    {
        $mailTransportCheck = static::getContainer()->get('oro_health_check.check.websocket');

        $this->assertInstanceOf(Success::class, $mailTransportCheck->check());
    }
}
