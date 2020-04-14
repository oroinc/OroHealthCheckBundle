<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Result\Success;

/**
 * There is always a positive result in a test mode since disable_delivery: true
 */
class MailTransportCheckTest extends WebTestCase
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
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'mail_transport'])
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }

    public function testServiceCheck()
    {
        $mailTransportCheck = static::getContainer()->get('oro_health_check.check.mail_transport');

        $this->assertInstanceOf(Success::class, $mailTransportCheck->check());
    }
}
