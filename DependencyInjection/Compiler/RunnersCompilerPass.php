<?php

namespace Oro\Bundle\HealthCheckBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Ensures that runners are use the logger reporter as default.
 */
class RunnersCompilerPass implements CompilerPassInterface
{
    #[\Override]
    public function process(ContainerBuilder $container)
    {
        if (!$container->hasParameter('liip_monitor.runners')
            || !$container->hasDefinition('oro_health_check.helper.logger_reporter')
        ) {
            return;
        }

        $runners = $container->getParameter('liip_monitor.runners');
        if (!$runners) {
            return;
        }

        $loggerReporter = $container->getDefinition('oro_health_check.helper.logger_reporter');
        foreach ($runners as $runner) {
            $runnerDefinition = $container->getDefinition($runner);
            $runnerDefinition->setArgument(0, null);
            $runnerDefinition->setArgument(1, null);
            $runnerDefinition->setArgument(2, $loggerReporter);
        }
    }
}
