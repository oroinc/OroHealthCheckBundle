<?php

namespace Oro\Bundle\HealthCheckBundle\Helper;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Collection as ResultsCollection;
use Laminas\Diagnostics\Result\FailureInterface;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\WarningInterface;
use Laminas\Diagnostics\Runner\Reporter\ReporterInterface;
use Psr\Log\LoggerInterface;

/**
 * Healthcheck reporter that logged all errors and warnings.
 */
class LoggerReporter implements ReporterInterface
{
    /**
     * @var LoggerInterface
     */
    protected $logger;

    /**
     * @param LoggerInterface $logger
     */
    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function onAfterRun(CheckInterface $check, ResultInterface $result, $checkAlias = null)
    {
        if ($result instanceof FailureInterface) {
            $data = $result->getData();
            $context = $data instanceof \Throwable ? $context = $data->getTrace() : [];
            $this->logger->error($this->createMessage($check, $result), $context);
        } elseif ($result instanceof WarningInterface) {
            $this->logger->warning($this->createMessage($check, $result));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function onStart(\ArrayObject $checks, $runnerConfig)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onBeforeRun(CheckInterface $check, $checkAlias = null)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onStop(ResultsCollection $results)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function onFinish(ResultsCollection $results)
    {
    }

    /**
     * @param CheckInterface $check
     * @param ResultInterface $result
     * @return string
     */
    protected function createMessage(CheckInterface $check, ResultInterface $result)
    {
        return sprintf('HEALTHCHECK: "%s". Message: "%s"', $check->getLabel(), $result->getMessage());
    }
}
