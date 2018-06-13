<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;

/**
 * Class for check mail transport configuration
 */
class MailTransportCheck implements CheckInterface
{
    /** @var \Swift_Mailer */
    protected $mailer;

    /**
     * @param \Swift_Mailer $mailer
     */
    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * @return Failure|Success
     */
    public function check(): ResultInterface
    {
        $this->mailer->getTransport()->start();

        return new Success();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Check if Mail Transport is available';
    }
}
