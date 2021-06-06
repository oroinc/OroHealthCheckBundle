<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\EventListener;

use Oro\Bundle\HealthCheckBundle\DependencyInjection\Compiler\RunnersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class RunnersCompilerPassTest extends \PHPUnit\Framework\TestCase
{
    /** @var RunnersCompilerPass */
    private $compiler;

    protected function setUp(): void
    {
        $this->compiler = new RunnersCompilerPass();
    }

    public function testProcessWithoutParameter()
    {
        $container = new ContainerBuilder();

        $this->compiler->process($container);
    }

    public function testProcessWithoutReporter()
    {
        $container = new ContainerBuilder();
        $container->setParameter('liip_monitor.runners', ['test_runner']);

        $this->compiler->process($container);
    }

    public function testProcessWithEmptyRunners()
    {
        $container = new ContainerBuilder();
        $container->setParameter('liip_monitor.runners', []);
        $container->register('oro_health_check.helper.logger_reporter');

        $this->compiler->process($container);
    }

    public function testProcess()
    {
        $container = new ContainerBuilder();
        $container->setParameter('liip_monitor.runners', ['test_runner1', 'test_runner2']);
        $reporterDef = $container->register('oro_health_check.helper.logger_reporter');
        $runner1Def = $container->register('test_runner1');
        $runner2Def = $container->register('test_runner2');

        $this->compiler->process($container);

        self::assertEquals([null, null, $reporterDef], $runner1Def->getArguments());
        self::assertEquals([null, null, $reporterDef], $runner2Def->getArguments());
    }
}
