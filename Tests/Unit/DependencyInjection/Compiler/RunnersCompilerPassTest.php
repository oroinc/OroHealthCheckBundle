<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\EventListener;

use Oro\Bundle\HealthCheckBundle\DependencyInjection\Compiler\RunnersCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

class RunnersCompilerPassTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @var RunnersCompilerPass
     */
    private $pass;

    protected function setUp()
    {
        $this->pass = new RunnersCompilerPass();
    }

    public function testProcessWithoutParameter()
    {
        /** @var ContainerBuilder|\PHPUnit\Framework\MockObject\MockObject $container */
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasParameter')
            ->with('liip_monitor.runners')
            ->willReturn(false);
        $container->expects($this->never())
            ->method('getParameter');
        $container->expects($this->never())
            ->method('getDefinition');

        $this->pass->process($container);
    }

    public function testProcessWithoutReporter()
    {
        /** @var ContainerBuilder|\PHPUnit\Framework\MockObject\MockObject $container */
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasParameter')
            ->with('liip_monitor.runners')
            ->willReturn(true);
        $container->expects($this->once())
            ->method('hasDefinition')
            ->with('oro_health_check.helper.logger_reporter')
            ->willReturn(false);
        $container->expects($this->never())
            ->method('getParameter');
        $container->expects($this->never())
            ->method('getDefinition');

        $this->pass->process($container);
    }

    public function testProcessWithEmptyRunners()
    {
        /** @var ContainerBuilder|\PHPUnit\Framework\MockObject\MockObject $container */
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasParameter')
            ->with('liip_monitor.runners')
            ->willReturn(true);
        $container->expects($this->once())
            ->method('hasDefinition')
            ->with('oro_health_check.helper.logger_reporter')
            ->willReturn(true);
        $container->expects($this->once())
            ->method('getParameter')
            ->with('liip_monitor.runners')
            ->willReturn([]);
        $container->expects($this->never())
            ->method('getDefinition');

        $this->pass->process($container);
    }

    public function testProcess()
    {
        /** @var ContainerBuilder|\PHPUnit\Framework\MockObject\MockObject $container */
        $container = $this->createMock(ContainerBuilder::class);
        $container->expects($this->once())
            ->method('hasParameter')
            ->with('liip_monitor.runners')
            ->willReturn(true);
        $container->expects($this->once())
            ->method('hasDefinition')
            ->with('oro_health_check.helper.logger_reporter')
            ->willReturn(true);
        $container->expects($this->once())
            ->method('getParameter')
            ->with('liip_monitor.runners')
            ->willReturn(['test_runner']);
        $reporter = new Definition();
        $runner = new Definition();
        $container->expects($this->exactly(2))
            ->method('getDefinition')
            ->withConsecutive(
                ['oro_health_check.helper.logger_reporter'],
                ['test_runner']
            )
            ->willReturnOnConsecutiveCalls($reporter, $runner);

        $this->pass->process($container);
        self::assertEquals(
            [0 => null, 1 => null, 2 => $reporter],
            $runner->getArguments()
        );
    }
}
