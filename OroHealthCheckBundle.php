<?php

namespace Oro\Bundle\HealthCheckBundle;

use Oro\Bundle\HealthCheckBundle\DependencyInjection\Compiler\MailTransportCheckPass;
use Oro\Bundle\HealthCheckBundle\DependencyInjection\Compiler\RunnersCompilerPass;
use Oro\Bundle\HealthCheckBundle\DependencyInjection\OroHealthCheckExtension;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * OroHealthCheckBundle implements a set of health checks for applications built on OroPlatform.
 */
class OroHealthCheckBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new OroHealthCheckExtension();
        }

        return $this->extension;
    }

    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RunnersCompilerPass(), PassConfig::TYPE_BEFORE_OPTIMIZATION, -1);
        $container->addCompilerPass(new MailTransportCheckPass());
    }
}
