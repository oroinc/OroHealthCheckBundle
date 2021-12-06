<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\MaintenanceBundle\Drivers\AbstractDriver;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class MaintenanceModeCheckTest extends WebTestCase
{
    private CheckInterface $check;

    private AbstractDriver $driver;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        $container = self::getContainer();

        $this->check = $container->get('oro_health_check.check.maintenance_mode');
        $this->driver = $container->get('oro_maintenance.driver.factory')->getDriver();
    }

    protected function tearDown(): void
    {
        $this->driver->unlock();
    }

    public function testExecuteApiCall(): void
    {
        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'maintenance_mode'])
        );

        self::assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }

    public function testExecuteApiCallInMaintenanceMode(): void
    {
        $this->driver->lock();

        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => 'maintenance_mode'])
        );

        $this->driver->unlock();

        self::assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
    }

    public function testServiceCheck(): void
    {
        self::assertEquals(new Success('Off'), $this->check->check());
    }

    public function testServiceCheckInMaintenanceMode(): void
    {
        $this->driver->lock();
        self::assertEquals(new Success('On'), $this->check->check());
        $this->driver->unlock();
    }
}
