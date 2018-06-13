<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Result\Success;

class FileStorageCheckTest extends WebTestCase
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
        $fileStorageChecks = static::getContainer()->get('oro_health_check.check.file_storage')->getChecks();
        foreach ($fileStorageChecks as $key => $check) {
            $this->client->request(
                'GET',
                $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => $key])
            );

            $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
        }
    }

    public function testServiceCheck()
    {
        $fileStorageChecks = static::getContainer()->get('oro_health_check.check.file_storage')->getChecks();
        foreach ($fileStorageChecks as $fileStorageCheck) {
            $result = $fileStorageCheck->check();

            $this->assertInstanceOf(Success::class, $result);
        }
    }
}
