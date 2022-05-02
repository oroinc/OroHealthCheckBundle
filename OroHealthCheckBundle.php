<?php

namespace Oro\Bundle\HealthCheckBundle;

use Oro\Bundle\HealthCheckBundle\DependencyInjection\Compiler\MailTransportCheckPass;
use Oro\Bundle\HealthCheckBundle\DependencyInjection\Compiler\RunnersCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OroHealthCheckBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container): void
    {
        parent::build($container);

        $container->addCompilerPass(new RunnersCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1);
        $container->addCompilerPass(new MailTransportCheckPass());
    }
}
