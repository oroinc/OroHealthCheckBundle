<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\RedisCheck;
use Oro\Bundle\HealthCheckBundle\Tests\Unit\Stub\PredisClient;
use ZendDiagnostics\Result\Success;

class RedisCheckTest extends \PHPUnit\Framework\TestCase
{
    /** @var PredisClient|\PHPUnit\Framework\MockObject\MockObject */
    protected $client;

    /** @var RedisCheck */
    protected $check;

    protected function setUp()
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
