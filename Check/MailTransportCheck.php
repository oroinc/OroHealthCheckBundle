<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Laminas\Diagnostics\Result\Warning;
use Oro\Bundle\EmailBundle\Mailer\Checker\ConnectionCheckerInterface;
use Symfony\Component\Mailer\Transport\Dsn;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Checks mail transport configuration.
 */
class MailTransportCheck implements CheckInterface
{
    public function __construct(
        private readonly string $transportDsn,
        private readonly ConnectionCheckerInterface $connectionChecker,
        private readonly TranslatorInterface $translator
    ) {
    }

    #[\Override]
    public function check(): Failure|Success|Warning
    {
        $dsn = Dsn::fromString($this->transportDsn);

        if (!$this->connectionChecker->supports($dsn)) {
            return new Warning($this->translator->trans(
                'oro.healthcheck.check.mail_transport_check.no_transport_connection_checkers.error'
            ));
        }

        if (!$this->connectionChecker->checkConnection($dsn)) {
            return new Failure($this->translator->trans(
                'oro.healthcheck.check.mail_transport_check.connection_failed.error'
            ));
        }

        return new Success();
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'Check if Mail Transport is available';
    }
}
