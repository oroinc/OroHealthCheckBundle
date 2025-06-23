<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\CronBundle\Command\CronCommand;
use Oro\Bundle\HealthCheckBundle\Check\CronCheck;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Psr\Cache\CacheItemPoolInterface;

/**
 * @dbIsolationPerTest
 */
class CronCheckTest extends WebTestCase
{
    private CronCheck $cronCheckService;
    private CacheItemPoolInterface $cronStateCache;

    protected function setUp(): void
    {
        $this->cronCheckService = self::getContainer()->get('oro_health_check.check.cron');
        $this->cronStateCache = self::getContainer()->get('oro_cron.state_cache');
    }

    public function testCronCheckSuccess(): void
    {
        // run cron command
        self::runCommand('oro:cron');

        $this->assertInstanceOf(Success::class, $this->cronCheckService->check());
    }

    public function testCronCheckFailed(): void
    {
        // delete item which sets when cron command is running
        $item = $this->cronStateCache->getItem(CronCommand::CRON_LAST_EXECUTION_DATA);
        $nowDateTime = new \DateTime('now', new \DateTimeZone('UTC'));
        $nowDateTime->modify('-1 hour');
        $item->set($nowDateTime);
        $this->cronStateCache->save($item);

        $this->assertInstanceOf(Failure::class, $this->cronCheckService->check());
        // delete outdated cache item
        $this->cronStateCache->deleteItem(CronCommand::CRON_LAST_EXECUTION_DATA);
    }

    public function testCronCheckInitWhenCacheClear(): void
    {
        // delete item which sets when cron command is running
        $this->cronStateCache->deleteItem(CronCommand::CRON_LAST_EXECUTION_DATA);

        $this->assertInstanceOf(Success::class, $this->cronCheckService->check());
    }
}
