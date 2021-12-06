<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\HealthCheckBundle\Check\RedisCheck;
use Oro\Bundle\HealthCheckBundle\Tests\Unit\Stub\PredisClient;

class RedisCheckTest extends \PHPUnit\Framework\TestCase
{
    /** @var PredisClient|\PHPUnit\Framework\MockObject\MockObject */
    private $client;

    /** @var RedisCheck */
    private $check;

    protected function setUp(): void
    {
        $this->client = $this->createMock(PredisClient::class);

        $this->check = new RedisCheck($this->client, 'Redis test config');
    }

    public function testCheck()
    {
        $this->client->expects($this->once())
            ->method('ping');

        $this->assertEquals(new Success(), $this->check->check());
    }

    public function testGetLabel()
    {
        $this->assertEquals('Check if Redis test config is available', $this->check->getLabel());
    }
}
