<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Skip;

/**
 * CI does not run clank server.
 * So, we can test only scenarios when checks are failed.
 */
class WebSocketCheckTest extends WebTestCase
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
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'websocket_frontend'])
        );
        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);

        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'websocket_backend'])
        );
        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_BAD_GATEWAY);
    }

    public function testServiceCheck()
    {
        $mailTransportCheck = static::getContainer()->get('oro_health_check.check.websocket_frontend');
        $this->assertInstanceOf(Skip::class, $mailTransportCheck->check());

        $mailTransportCheck = static::getContainer()->get('oro_health_check.check.websocket_backend');
        $this->assertInstanceOf(Failure::class, $mailTransportCheck->check());
    }
}
