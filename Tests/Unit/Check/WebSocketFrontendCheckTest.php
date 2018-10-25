<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\WebSocketFrontendCheck;
use Oro\Bundle\SyncBundle\Client\ConnectionChecker;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;

class WebSocketFrontendCheckTest extends \PHPUnit\Framework\TestCase
{
    /** @var ConnectionChecker|\PHPUnit\Framework\MockObject\MockObject */
    private $checkerFrontend;

    /** @var WebSocketFrontendCheck */
    private $check;

    protected function setUp()
    {
        $this->checkerFrontend = $this->createMock(ConnectionChecker::class);

        $this->check = new WebSocketFrontendCheck($this->checkerFrontend);
    }

    public function testCheck(): void
    {
        $this->checkerFrontend->expects(self::once())
            ->method('checkConnection')
            ->willReturn(true);

        $this->checkerFrontend->expects(self::once())
            ->method('checkConnection')
            ->willReturn(true);

        self::assertEquals(new Success(), $this->check->check());
    }

    public function testCheckFailure(): void
    {
        $this->checkerFrontend->expects(self::once())
            ->method('checkConnection')
            ->willReturn(false);

        self::assertEquals(new Warning('Not available'), $this->check->check());
    }

    public function testGetLabel(): void
    {
        self::assertEquals('Check if WebSocket frontend connection can be established', $this->check->getLabel());
    }
}
