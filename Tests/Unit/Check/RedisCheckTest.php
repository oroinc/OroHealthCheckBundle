<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\HealthCheckBundle\Check\RedisCheck;
use Oro\Bundle\HealthCheckBundle\Tests\Unit\Stub\PredisClient;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Predis\Response\Status;

class RedisCheckTest extends TestCase
{
    private PredisClient|MockObject $client;

    private RedisCheck $check;

    protected function setUp(): void
    {
        $this->client = $this->createMock(PredisClient::class);

        $this->check = new RedisCheck($this->client, 'Redis test config');
    }

    public function testCheckFailureWithException(): void
    {
        $this->client
            ->expects(self::once())
            ->method('ping')
            ->willThrowException(new \Exception());

        $this->assertEquals(new Failure(), $this->check->check());
    }

    public function testCheckFailure(): void
    {
        $this->client
            ->expects(self::once())
            ->method('ping')
            ->willReturn(null);

        $this->assertEquals(new Failure(), $this->check->check());
    }

    public function testCheckSuccess(): void
    {
        $statusMock = $this->createMock(Status::class);

        $statusMock
            ->expects(self::once())
            ->method('getPayload')
            ->willReturn('PONG');

        $this->client
            ->expects(self::once())
            ->method('ping')
            ->willReturn($statusMock);

        $this->assertEquals(new Success(), $this->check->check());
    }

    public function testGetLabel(): void
    {
        $this->assertEquals('Check if Redis test config is available', $this->check->getLabel());
    }
}
