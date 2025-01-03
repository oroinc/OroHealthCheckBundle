<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class FileStorageCheckTest extends WebTestCase
{
    #[\Override]
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testExecuteApiCall()
    {
        $fileStorageChecks = self::getContainer()->get('oro_health_check.check.file_storage')->getChecks();
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
        $fileStorageChecks = self::getContainer()->get('oro_health_check.check.file_storage')->getChecks();
        $expectedKeys = [
            'fs_var_cache_prod',
            'fs_var_logs',
            'fs_var_data',
            'fs_public_media'
        ];
        foreach ($fileStorageChecks as $key => $fileStorageCheck) {
            $result = $fileStorageCheck->check();

            $this->assertInstanceOf(Success::class, $result);
            $this->assertEquals(array_shift($expectedKeys), $key);
        }
    }
}
