<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Result\Skip;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

/**
 * CI does not run websocket server.
 * So, we can test only scenarios when checks are failed.
 */
class WebSocketCheckTest extends WebTestCase
{
    #[\Override]
    protected function setUp(): void
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
        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }

    public function testServiceCheck()
    {
        $mailTransportCheck = self::getContainer()->get('oro_health_check.check.websocket_frontend');
        $this->assertInstanceOf(Skip::class, $mailTransportCheck->check());

        $mailTransportCheck = self::getContainer()->get('oro_health_check.check.websocket_backend');
        $this->assertInstanceOf(Skip::class, $mailTransportCheck->check());
    }
}
