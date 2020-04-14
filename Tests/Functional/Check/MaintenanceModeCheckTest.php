<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Lexik\Bundle\MaintenanceBundle\Drivers\AbstractDriver;
use Oro\Bundle\HealthCheckBundle\Drivers\FileDriver;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class MaintenanceModeCheckTest extends WebTestCase
{
    /** @var CheckInterface */
    private $check;

    /** @var AbstractDriver */
    private $driver;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        $container = static::getContainer();

        $this->check = $container->get('oro_health_check.check.maintenance_mode');
        $this->driver = $container->get('lexik_maintenance.driver.factory')->getDriver();
    }

    protected function tearDown(): void
    {
        $this->driver->unlock();
    }

    public function testExecuteApiCall()
    {
        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'maintenance_mode'])
        );

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }

    public function testExecuteApiCallInMaintenanceMode()
    {
        $this->driver->lock();

        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'maintenance_mode'])
        );

        $this->driver->unlock();

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }

    public function testExecuteApiCallInExpiredMaintenanceMode()
    {
        $expectedResponseCode = Response::HTTP_OK;
        if ($this->driver instanceof FileDriver) {
            $this->driver->setTtl(1);
            $expectedResponseCode = Response::HTTP_BAD_GATEWAY;
        }

        $this->driver->lock();
        sleep(2);
        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'maintenance_mode'])
        );

        $this->driver->unlock();

        $this->assertResponseStatusCodeEquals($this->client->getResponse(), $expectedResponseCode);
    }

    public function testServiceCheck()
    {
        $this->assertEquals(new Success('Off'), $this->check->check());
    }

    public function testServiceCheckInMaintenanceMode()
    {
        $this->driver->lock();
        $this->assertEquals(new Success('On'), $this->check->check());
        $this->driver->unlock();
    }

    public function testServiceCheckInExpiredMaintenanceMode()
    {
        $expectedReturnInstance = new Success('On');
        if ($this->driver instanceof FileDriver) {
            $this->driver->setTtl(1);
            $expectedReturnInstance = new Failure('Expired');
        }

        $this->driver->lock();
        sleep(2);

        $result = $this->check->check();

        $this->driver->unlock();

        $this->assertEquals($expectedReturnInstance, $result);
    }
}
