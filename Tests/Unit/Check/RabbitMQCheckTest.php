<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Laminas\Diagnostics\Result\Skip;
use Oro\Bundle\HealthCheckBundle\Check\RabbitMQCheck;
use Oro\Component\AmqpMessageQueue\Provider\TransportConnectionConfigProvider;

class RabbitMQCheckTest extends \PHPUnit\Framework\TestCase
{
    public function testCheckNotConfiguredWithArray()
    {
        $configProviderMock = $this->createMock(TransportConnectionConfigProvider::class);
        $configProviderMock->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([]);
        $check = new RabbitMQCheck($configProviderMock);

        $this->assertEquals(new Skip('RabbitMQ connection is not configured. Check Skipped.'), $check->check());
    }

    public function testCheckNotConfiguredProvider()
    {
        $check = new RabbitMQCheck(null);

        $this->assertEquals(new Skip('RabbitMQ connection is not configured. Check Skipped.'), $check->check());
    }

    public function testGetLabel()
    {
        $check = new RabbitMQCheck(null);

        $this->assertEquals('Check if RabbitMQ is available in case it is configured', $check->getLabel());
    }
}
