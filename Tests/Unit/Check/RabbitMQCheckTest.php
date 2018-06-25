<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\RabbitMQCheck;
use ZendDiagnostics\Result\Skip;

class RabbitMQCheckTest extends \PHPUnit\Framework\TestCase
{
    public function testCheckNotConfiguredWithArray()
    {
        $check = new RabbitMQCheck([]);

        $this->assertEquals(new Skip('RabbitMQ connection is not configured. Check Skipped.'), $check->check());
    }

    public function testCheckNotConfiguredWithNull()
    {
        $check = new RabbitMQCheck(null);

        $this->assertEquals(new Skip('RabbitMQ connection is not configured. Check Skipped.'), $check->check());
    }

    public function testGetLabel()
    {
        $check = new RabbitMQCheck([]);

        $this->assertEquals('Check if RabbitMQ is available in case it is configured', $check->getLabel());
    }
}
