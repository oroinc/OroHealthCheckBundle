<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\WebSocketCheck;
use Oro\Bundle\SyncBundle\Client\ConnectionChecker;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class WebSocketCheckTest extends \PHPUnit_Framework_TestCase
{
    /** @var ConnectionChecker|\PHPUnit_Framework_MockObject_MockObject */
    protected $connectionChecker;

    /** @var WebSocketCheck */
    protected $check;

    protected function setUp()
    {
        $this->connectionChecker = $this->createMock(ConnectionChecker::class);

        $this->check = new WebSocketCheck($this->connectionChecker);
    }

    public function testCheck()
    {
        $this->connectionChecker->expects($this->once())
            ->method('checkConnection')
            ->willReturn(true);

        $this->assertEquals(new Success(), $this->check->check());
    }

    public function testCheckFailure()
    {
        $this->connectionChecker->expects($this->once())
            ->method('checkConnection')
            ->willReturn(false);

        $this->assertEquals(new Failure('Not available'), $this->check->check());
    }

    public function testGetLabel()
    {
        $this->assertEquals('Check if WebSocket server is available', $this->check->getLabel());
    }
}
