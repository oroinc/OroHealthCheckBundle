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
        $expectedKeys = [
            'fs_var_cache_prod',
            'fs_var_logs',
            'fs_public_media',
            'fs_public_uploads',
            'fs_var_attachment',
            'fs_var_import_export'
        ];
        foreach ($fileStorageChecks as $key => $fileStorageCheck) {
            $result = $fileStorageCheck->check();

            $this->assertInstanceOf(Success::class, $result);
            $this->assertEquals(array_shift($expectedKeys), $key);
        }
    }
}
