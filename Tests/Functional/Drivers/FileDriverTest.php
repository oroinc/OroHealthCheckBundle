<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\HealthCheckBundle\Drivers\FileDriver;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class FileDriverTest extends WebTestCase
{
    /** @var FileDriver */
    protected $driver;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());
        $this->driver = static::getContainer()->get('lexik_maintenance.driver.factory')->getDriver();
        if (!$this->driver instanceof FileDriver) {
            $this->markTestSkipped('Skipping test, another driver is used');
        }
    }

    protected function tearDown(): void
    {
        $this->driver->unlock();
    }

    public function testMaintenanceLockWithTtl()
    {
        $this->driver->lock();
        $this->assertTrue($this->driver->hasTtl());
        $this->assertNotNull($this->driver->getTtl());
        $this->assertTrue($this->driver->isExists());
        $this->assertFalse($this->driver->isExpired());
        $this->driver->unlock();
    }

    public function testMaintenanceModeIsExpired()
    {
        $this->driver->setTtl(1);
        $this->driver->lock();
        $this->assertTrue($this->driver->hasTtl());
        $this->assertNotNull($this->driver->getTtl());
        $this->assertTrue($this->driver->isExists());
        sleep(2);
        $this->assertTrue($this->driver->isExpired());
        $this->driver->unlock();
    }
}
