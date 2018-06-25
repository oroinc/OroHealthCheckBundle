<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\MailTransportCheck;
use ZendDiagnostics\Result\Success;

class MailTransportCheckTest extends \PHPUnit\Framework\TestCase
{
    /** @var \Swift_Transport|\PHPUnit\Framework\MockObject\MockObject */
    protected $transport;

    /** @var MailTransportCheck */
    protected $check;

    protected function setUp()
    {
        $this->transport = $this->createMock(\Swift_Transport::class);

        /** @var \Swift_Mailer|\PHPUnit\Framework\MockObject\MockObject $mailer */
        $mailer = $this->createMock(\Swift_Mailer::class);
        $mailer->expects($this->any())
            ->method('getTransport')
            ->willReturn($this->transport);

        $this->check = new MailTransportCheck($mailer);
    }

    public function testCheck()
    {
        $this->transport->expects($this->once())
            ->method('start');

        $this->assertEquals(new Success(), $this->check->check());
    }

    public function testGetLabel()
    {
        $this->assertEquals('Check if Mail Transport is available', $this->check->getLabel());
    }
}
