<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\WebSocketCheck;
use Oro\Bundle\SyncBundle\Client\ConnectionChecker;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class WebSocketCheckTest extends \PHPUnit\Framework\TestCase
{
    /** @var ConnectionChecker|\PHPUnit\Framework\MockObject\MockObject */
    protected $checkerBackend;

    /** @var ConnectionChecker|\PHPUnit\Framework\MockObject\MockObject */
    protected $checkerFrontend;

    /** @var WebSocketCheck */
    protected $check;

    protected function setUp()
    {
        $this->checkerBackend = $this->createMock(ConnectionChecker::class);
        $this->checkerFrontend = $this->createMock(ConnectionChecker::class);

        $this->check = new WebSocketCheck($this->checkerBackend, $this->checkerFrontend);
    }

    public function testCheck()
    {
        $this->checkerBackend->expects($this->once())
            ->method('checkConnection')
            ->willReturn(true);

        $this->checkerFrontend->expects($this->once())
            ->method('checkConnection')
            ->willReturn(true);

        $this->assertEquals(new Success(), $this->check->check());
    }

    public function testCheckBackendFailure()
    {
        $this->checkerBackend->expects($this->once())
            ->method('checkConnection')
            ->willReturn(false);

        $this->checkerFrontend->expects($this->never())
            ->method($this->anything());

        $this->assertEquals(new Failure('Not available'), $this->check->check());
    }

    public function testCheckFrontendFailure()
    {
        $this->checkerBackend->expects($this->once())
            ->method('checkConnection')
            ->willReturn(true);

        $this->checkerFrontend->expects($this->once())
            ->method('checkConnection')
            ->willReturn(false);

        $this->assertEquals(new Failure('Not available'), $this->check->check());
    }

    public function testGetLabel()
    {
        $this->assertEquals('Check if WebSocket server is available', $this->check->getLabel());
    }
}
