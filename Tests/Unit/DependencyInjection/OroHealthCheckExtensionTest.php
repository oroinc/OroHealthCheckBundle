<?php

declare(strict_types=1);

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\HealthCheckBundle\DependencyInjection\OroHealthCheckExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OroHealthCheckExtensionTest extends \PHPUnit\Framework\TestCase
{
    public function testLoad(): void
    {
        $container = new ContainerBuilder();
        $container->setParameter('oro_maintenance.driver', ['options' => ['file_path' => 'path1']]);
        $container->setParameter('kernel.environment', 'prod');

        $configs = [
            ['maintenance_driver' => ['options' => ['file_path' => 'path2']]],
        ];

        $extension = new OroHealthCheckExtension();
        $extension->load($configs, $container);

        self::assertEquals(
            ['options' => ['file_path' => 'path2']],
            $container->getParameter('oro_maintenance.driver')
        );
    }
}
