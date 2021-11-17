<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\DependencyInjection\Compiler;

use Oro\Bundle\HealthCheckBundle\Check\MailTransportCheck;
use Oro\Bundle\HealthCheckBundle\DependencyInjection\Compiler\MailTransportCheckPass;
use Symfony\Component\DependencyInjection\Argument\AbstractArgument;
use Symfony\Component\DependencyInjection\Compiler\ResolveNamedArgumentsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\Mailer\Transport\Transports;

class MailTransportCheckPassTest extends \PHPUnit\Framework\TestCase
{
    public function testProcessDoesNothingWhenNoMailerTransports(): void
    {
        $containerBuilder = new ContainerBuilder();
        $mailTransportChecker = new Definition(
            MailTransportCheck::class,
            [
                new AbstractArgument(),
                new Reference('oro_email.mailer.checker.connection_checkers'),
                new Reference('translator'),
            ]
        );
        $containerBuilder->setDefinition('oro_health_check.check.mail_transport', $mailTransportChecker);

        (new MailTransportCheckPass())->process($containerBuilder);
        (new ResolveNamedArgumentsPass())->process($containerBuilder);

        self::assertEquals(
            $mailTransportChecker,
            $containerBuilder->getDefinition('oro_health_check.check.mail_transport')
        );
    }

    public function testProcessSetsTransportDsnArgument(): void
    {
        $containerBuilder = new ContainerBuilder();
        $mailTransportCheckDef = new Definition(
            MailTransportCheck::class,
            [
                new AbstractArgument(),
                new Reference('oro_email.mailer.checker.connection_checkers'),
                new Reference('translator'),
            ]
        );
        $transportDsn = ['main' => 'null://null'];
        $mailerTransportsDef = new Definition(Transports::class, [$transportDsn]);
        $containerBuilder->setDefinition('oro_health_check.check.mail_transport', $mailTransportCheckDef);
        $containerBuilder->setDefinition('mailer.transports', $mailerTransportsDef);

        (new MailTransportCheckPass())->process($containerBuilder);
        // Resolves $transportDsn named argument to the corresponding index based on class constructor.
        (new ResolveNamedArgumentsPass())->process($containerBuilder);

        self::assertEquals(
            new Definition(
                MailTransportCheck::class,
                [
                    $transportDsn['main'],
                    new Reference('oro_email.mailer.checker.connection_checkers'),
                    new Reference('translator'),
                ]
            ),
            $containerBuilder->getDefinition('oro_health_check.check.mail_transport')
        );
    }
}
