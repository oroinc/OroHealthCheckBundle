<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\WebSocketCheck;
use Oro\Bundle\SyncBundle\Wamp\TopicPublisher;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\Success;

class WebSocketCheckTest extends \PHPUnit_Framework_TestCase
{
    /** @var TopicPublisher|\PHPUnit_Framework_MockObject_MockObject */
    protected $topicPublisher;

    /** @var WebSocketCheck */
    protected $check;

    protected function setUp()
    {
        $this->topicPublisher = $this->createMock(TopicPublisher::class);

        $this->check = new WebSocketCheck([$this->topicPublisher]);
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
        $this->topicPublisher->expects($this->once())
            ->method('check')
            ->willReturn(true);

        $this->assertEquals(new Success(), $this->check->check());
    }

    public function testCheckFailure()
    {
        $this->topicPublisher->expects($this->once())
            ->method('check')
            ->willReturn(false);

        $this->assertEquals(new Failure('Not available'), $this->check->check());
    }

    public function testGetLabel()
    {
        $this->assertEquals('Check if WebSocket server is available', $this->check->getLabel());
    }
}
