<?php
declare(strict_types=1);

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\HealthCheckBundle\DependencyInjection\OroHealthCheckExtension;
use Oro\Bundle\TestFrameworkBundle\Test\DependencyInjection\ExtensionTestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class OroHealthCheckExtensionTest extends ExtensionTestCase
{
    protected OroHealthCheckExtension $extension;

    public function testLoad(): void
    {
        $this->loadExtension(new OroHealthCheckExtension());

        $this->assertDefinitionsLoaded(
            [
                'oro_health_check.check.file_storage',
                'oro_health_check.check.redis',
                'oro_health_check.check.doctrine_dbal',
                'oro_health_check.check.mail_transport',
                'oro_health_check.check.rabbitmq',
                'oro_health_check.websocket_client.frontend',
                'oro_health_check.client.connection_checker.frontend',
                'oro_health_check.check.websocket_backend',
                'oro_health_check.check.websocket_frontend',
                'oro_health_check.check.maintenance_mode',
            ]
        );
        $this->assertParametersLoaded(['oro_maintenance.driver']);
        static::assertEquals(
            [
                'options' => [
                    'file_path' => 'test/file/path'
                ]
            ],
            $this->actualParameters['oro_maintenance.driver']
        );
    }

    protected function getContainerMock(): ContainerBuilder
    {
        $containerBuilder = parent::getContainerMock();
        $containerBuilder
            ->method('getParameter')
            ->with('oro_maintenance.driver')
            ->willReturn(
                [
                    'options' => [
                        'file_path' => 'test/file/path'
                    ]
                ]
            );

        return $containerBuilder;
    }
}
