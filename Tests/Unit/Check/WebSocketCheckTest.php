<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\HealthCheckBundle\Check\WebSocketCheck;
use Oro\Bundle\SyncBundle\Client\ConnectionChecker;
use PHPUnit\Framework\MockObject\MockObject;

class WebSocketCheckTest extends \PHPUnit\Framework\TestCase
{
    private ConnectionChecker|MockObject $checker;

    #[\Override]
    protected function setUp(): void
    {
        $this->checker = $this->createMock(ConnectionChecker::class);
    }

    public function testCheck(): void
    {
        $check = new WebSocketCheck($this->checker, 'test');

        $this->checker->expects(self::never())
            ->method('isConfigured');
        $this->checker->expects(self::once())
            ->method('checkConnection')
            ->willReturn(true);

        self::assertEquals(new Success(), $check->check());
    }

    public function testCheckWhenNotConfigured(): void
    {
        $check = new WebSocketCheck($this->checker, 'test');

        $this->checker->expects(self::once())
            ->method('isConfigured')
            ->willReturn(false);
        $this->checker->expects(self::once())
            ->method('checkConnection')
            ->willReturn(false);

        self::assertEquals(new Skip('Not available. Skipped as this check is not mandatory.'), $check->check());
    }

    public function testCheckFailure(): void
    {
        $check = new WebSocketCheck($this->checker, 'test');

        $this->checker->expects(self::once())
            ->method('isConfigured')
            ->willReturn(true);
        $this->checker->expects(self::once())
            ->method('checkConnection')
            ->willReturn(false);

        self::assertEquals(new Failure('Not available.'), $check->check());
    }

    public function testCheckSkip(): void
    {
        $check = new WebSocketCheck($this->checker, 'test', false);

        $this->checker->expects(self::once())
            ->method('isConfigured')
            ->willReturn(true);
        $this->checker->expects(self::once())
            ->method('checkConnection')
            ->willReturn(false);

        self::assertEquals(new Skip('Not available. Skipped as this check is not mandatory.'), $check->check());
    }

    public function testGetLabel(): void
    {
        $check = new WebSocketCheck($this->checker, 'test');

        self::assertEquals('Check if WebSocket test connection can be established', $check->getLabel());
    }
}
