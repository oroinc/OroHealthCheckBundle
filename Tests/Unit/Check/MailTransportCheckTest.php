<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;
use Oro\Bundle\EmailBundle\Mailer\Checker\ConnectionCheckerInterface;
use Oro\Bundle\HealthCheckBundle\Check\MailTransportCheck;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Contracts\Translation\TranslatorInterface;

class MailTransportCheckTest extends \PHPUnit\Framework\TestCase
{
    private ConnectionCheckerInterface|\PHPUnit\Framework\MockObject\MockObject $connectionChecker;

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
            ->willReturnCallback(static function (Dsn $dsn, string &$error = null) {
                $error = 'Error message';

                return false;
            });

        self::assertEquals(new Failure('Error message'), $this->check->check());
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
