<?php

namespace Oro\Bundle\HealthCheckBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Defines 'transportDsn' abstract argument for 'oro_health_check.check.mail_transport' service.
 */
class MailTransportCheckPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        if (!$container->hasDefinition('mailer.transports')) {
            return;
        }

        $mailerTransportsDef = $container->getDefinition('mailer.transports');
        $transportsDsns = $mailerTransportsDef->getArgument(0);
        // The first transport DSN is considered as the default one.
        // @see \Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension::registerMailerConfiguration
        $defaultTransportDsn = reset($transportsDsns);

        $container
            ->getDefinition('oro_health_check.check.mail_transport')
            ->setArgument('$transportDsn', $defaultTransportDsn);
    }
}
