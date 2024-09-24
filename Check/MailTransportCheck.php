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
 * Class for check mail transport configuration
 */
class MailTransportCheck implements CheckInterface
{
    private string $transportDsn;

    private ConnectionCheckerInterface $connectionChecker;

    private TranslatorInterface $translator;

    public function __construct(
        string $transportDsn,
        ConnectionCheckerInterface $connectionChecker,
        TranslatorInterface $translator
    ) {
        $this->transportDsn = $transportDsn;
        $this->connectionChecker = $connectionChecker;
        $this->translator = $translator;
    }

    #[\Override]
    public function check(): Failure|Success|Warning
    {
        $dsn = Dsn::fromString($this->transportDsn);

        if (!$this->connectionChecker->supports($dsn)) {
            return new Warning(
                $this->translator->trans(
                    'oro.healthcheck.check.mail_transport_check.no_transport_connection_checkers.error'
                )
            );
        }

        return $this->connectionChecker->checkConnection($dsn, $connectionError)
            ? new Success()
            : new Failure($connectionError);
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'Check if Mail Transport is available';
    }
}
