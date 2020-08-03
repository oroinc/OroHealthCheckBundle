<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class DoctrineDbalCheckTest extends WebTestCase
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
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'doctrine_dbal'])
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }

    public function testServiceCheck()
    {
        $doctrineDbalCheck = static::getContainer()->get('oro_health_check.check.doctrine_dbal');

        $this->assertInstanceOf(Success::class, $doctrineDbalCheck->check());
    }
}
