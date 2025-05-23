<?php

namespace Oro\Bundle\HealthCheckBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class OroHealthCheckExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $config = $this->processConfiguration($this->getConfiguration($configs, $container), $configs);
        $this->configurationOverride($config, $container);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('search.yml');

        if ('test' === $container->getParameter('kernel.environment')) {
            $loader->load('services_test.yml');
        }
    }

    /**
     * Default driver for OroMaintenanceBundle should be force-overridden if OroHealthCheckBundle is enabled
     */
    private function configurationOverride(array $config, ContainerBuilder $container): void
    {
        /** @var array $originalConfig */
        $originalConfig = $container->getParameter('oro_maintenance.driver');

        $maintenanceDriverConfig = $config['maintenance_driver'];
        $maintenanceDriverConfig['options'] = array_merge(
            $originalConfig['options'],
            $maintenanceDriverConfig['options']
        );

        $container->setParameter('oro_maintenance.driver', $maintenanceDriverConfig);

        if (isset($config['last_cron_execution_cache']['ttl'])) {
            $container->setParameter(
                'oro_health_check.last_cron_execution_cache.ttl',
                $config['last_cron_execution_cache']['ttl']
            );
        }
    }
}
