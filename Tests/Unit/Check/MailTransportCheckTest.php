<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;
use Oro\Bundle\EmailBundle\Mailer\Checker\ConnectionCheckerInterface;
use Oro\Bundle\HealthCheckBundle\Check\MailTransportCheck;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailTransportCheckTest extends TestCase
{
    private ConnectionCheckerInterface&MockObject $connectionChecker;
    private MailTransportCheck $check;

    #[\Override]
    protected function setUp(): void
    {
        $this->connectionChecker = $this->createMock(ConnectionCheckerInterface::class);

        $translator = $this->createMock(TranslatorInterface::class);
        $translator->expects(self::any())
            ->method('trans')
            ->withAnyParameters()
            ->willReturnCallback(static fn ($id) => $id . '.translated');

        $this->check = new MailTransportCheck('null://null', $this->connectionChecker, $translator);
    }

    public function testCheckNoSupportedCheckers(): void
    {
        $this->connectionChecker->expects(self::once())
            ->method('supports')
            ->with(Dsn::fromString('null://null'))
            ->willReturn(false);

        self::assertEquals(
            new Warning('oro.healthcheck.check.mail_transport_check.no_transport_connection_checkers.error.translated'),
            $this->check->check()
        );
    }

    public function testCheckWithErrors(): void
    {
        $this->connectionChecker->expects(self::once())
            ->method('supports')
            ->with(Dsn::fromString('null://null'))
            ->willReturn(true);

        $this->connectionChecker->expects(self::once())
            ->method('checkConnection')
            ->with(Dsn::fromString('null://null'))
            ->willReturn(false);

        self::assertEquals(
            new Failure('oro.healthcheck.check.mail_transport_check.connection_failed.error.translated'),
            $this->check->check()
        );
    }

    public function testCheckSuccess(): void
    {
        $this->connectionChecker->expects(self::once())
            ->method('supports')
            ->with(Dsn::fromString('null://null'))
            ->willReturn(true);

        $this->connectionChecker->expects(self::once())
            ->method('checkConnection')
            ->with(Dsn::fromString('null://null'))
            ->willReturn(true);

        self::assertEquals(new Success(), $this->check->check());
    }

    public function testGetLabel(): void
    {
        self::assertEquals('Check if Mail Transport is available', $this->check->getLabel());
    }
}
