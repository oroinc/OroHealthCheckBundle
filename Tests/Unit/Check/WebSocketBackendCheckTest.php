<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\WebSocketBackendCheck;
use Oro\Bundle\SyncBundle\Client\ConnectionChecker;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class WebSocketBackendCheckTest extends \PHPUnit\Framework\TestCase
{
    /** @var ConnectionChecker|\PHPUnit\Framework\MockObject\MockObject */
    private $checkerBackend;

    /** @var WebSocketBackendCheck */
    private $check;

    protected function setUp()
    {
        $this->checkerBackend = $this->createMock(ConnectionChecker::class);

        $this->check = new WebSocketBackendCheck($this->checkerBackend);
    }

    public function testCheck(): void
    {
        $this->checkerBackend->expects(self::once())
            ->method('checkConnection')
            ->willReturn(true);

        self::assertEquals(new Success(), $this->check->check());
    }

    public function testCheckFailure(): void
    {
        $this->checkerBackend->expects(self::once())
            ->method('checkConnection')
            ->willReturn(false);

        self::assertEquals(new Failure('Not available'), $this->check->check());
    }

    public function testGetLabel(): void
    {
        self::assertEquals('Check if WebSocket backend connection can be established', $this->check->getLabel());
    }
}
