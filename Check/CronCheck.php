<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\CronBundle\Command\CronCommand;
use Psr\Cache\CacheItemPoolInterface;

/**
 * Checks if cron was running within the CRON_LAST_EXECUTION_DATA, or the value provided by the semantic configuration.
 */
class CronCheck implements CheckInterface
{
    public function __construct(private CacheItemPoolInterface $cache, private int $cronLastExecutionCacheTtl)
    {
    }

    #[\Override]
    public function check(): ResultInterface
    {
        $nowDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        if (!$this->cache->hasItem(CronCommand::CRON_LAST_EXECUTION_DATA)) {
            $item = $this->cache->getItem(CronCommand::CRON_LAST_EXECUTION_DATA);
            $item->set($nowDateTime);
            $this->cache->save($item);

            return new Success();
        }
        // checks if last cron execution is not expired
        $lastExecutionItem = $this->cache->getItem(CronCommand::CRON_LAST_EXECUTION_DATA);
        $lastExecution = $lastExecutionItem->get();

        if (!$lastExecution instanceof \DateTime
            || ($nowDateTime->getTimestamp() - $lastExecution->getTimestamp()) > $this->cronLastExecutionCacheTtl) {
            return new Failure();
        }

        return new Success();
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'Check if Cron is running';
    }
}
