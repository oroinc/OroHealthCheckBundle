<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\HealthCheckBundle\Check\MongoCheck;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;

class MongoCheckTest extends WebTestCase
{
    private MongoCheck $mongoDbCheck;

    protected function setUp(): void
    {
        $this->mongoDbCheck = self::getContainer()->get('oro_health_check.check.mongo_db');

        if (!self::getContainer()->has('oro.mongodb.driver.config.gaufrette.public_adapter')) {
            $this->markTestSkipped('MongoDB is not configured.');
        }
    }

    public function testCheckSuccess(): void
    {
        $this->assertInstanceOf(Success::class, $this->mongoDbCheck->check());
    }
}
