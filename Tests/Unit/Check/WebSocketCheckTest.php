<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\WebSocketCheck;
use Oro\Bundle\SyncBundle\Wamp\TopicPublisher;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Warning;

class WebSocketCheckTest extends \PHPUnit_Framework_TestCase
{
    /** @var TopicPublisher|\PHPUnit_Framework_MockObject_MockObject */
    private $backendTopicPublisher;

    /** @var TopicPublisher|\PHPUnit_Framework_MockObject_MockObject */
    private $frontendTopicPublisher;

    /** @var WebSocketCheck */
    private $check;

    protected function setUp()
    {
        $this->backendTopicPublisher = $this->createMock(TopicPublisher::class);
        $this->frontendTopicPublisher = $this->createMock(TopicPublisher::class);

        $this->check = new WebSocketCheck([$this->backendTopicPublisher, $this->frontendTopicPublisher]);
    }

    public function testException()
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage(
            'Topic publisher must be instance of "Oro\Bundle\SyncBundle\Wamp\TopicPublisher", "stdClass" given.'
        );

        $this->check = new WebSocketCheck([new \stdClass()]);
    }

    public function testCheck()
    {
        $this->backendTopicPublisher->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->frontendTopicPublisher->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->assertEquals(new Success(), $this->check->check());
    }

    public function testCheckBackendFailure()
    {
        $this->backendTopicPublisher->expects($this->once())
            ->method('check')
            ->willReturn(false);

        $this->frontendTopicPublisher->expects($this->never())
            ->method('check');

        $this->assertEquals(new Failure('Not available'), $this->check->check());
    }

    public function testCheckFrontendFailure()
    {
        $this->backendTopicPublisher->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->frontendTopicPublisher->expects($this->once())
            ->method('check')
            ->willReturn(false);

        $this->assertEquals(
            new Warning('WebSocket backend connection works, but frontend connection cannot be established'),
            $this->check->check()
        );
    }

    public function testGetLabel()
    {
        $this->assertEquals('Check if WebSocket server is available', $this->check->getLabel());
    }
}
