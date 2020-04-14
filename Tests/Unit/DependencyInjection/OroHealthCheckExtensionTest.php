<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\DependencyInjection;

use Oro\Bundle\HealthCheckBundle\DependencyInjection\OroHealthCheckExtension;
use Oro\Bundle\HealthCheckBundle\Drivers\FileDriver;
use Oro\Bundle\TestFrameworkBundle\Test\DependencyInjection\ExtensionTestCase;

class OroHealthCheckExtensionTest extends ExtensionTestCase
{
    /** @var OroHealthCheckExtension */
    protected $extension;

    protected function setUp(): void
    {
        $this->extension = new OroHealthCheckExtension();
    }

    public function testLoad()
    {
        $this->loadExtension($this->extension);

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
        $this->assertParametersLoaded(['lexik_maintenance.driver']);
        $this->assertEquals(
            [
                'class' => FileDriver::class,
                'options' => [
                    'ttl' => 600,
                    'file_path' => 'test/file/path'
                ]
            ],
            $this->actualParameters['lexik_maintenance.driver']
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getContainerMock()
    {
        $container = parent::getContainerMock();
        $container->expects($this->any())
            ->method('getParameter')
            ->with('lexik_maintenance.driver')
            ->willReturn(
                [
                    'options' => [
                        'file_path' => 'test/file/path'
                    ]
                ]
            );

        return $container;
    }
}
