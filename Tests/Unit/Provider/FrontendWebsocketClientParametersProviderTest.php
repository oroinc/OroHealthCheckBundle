<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Provider;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\HealthCheckBundle\Provider\FrontendWebsocketClientParametersProvider;
use Oro\Bundle\SyncBundle\Provider\WebsocketClientParametersProvider;

class FrontendWebsocketClientParametersProviderTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider properConfigParametersProvider
     */
    public function testProperConfigParametersProcessing(
        array $constructorArguments,
        string $host,
        int $port,
        string $path,
        string $transport,
        array $contextOptions
    ): void {
        $frontendWsParametersProvider = new FrontendWebsocketClientParametersProvider(
            $constructorArguments['innerProvider'],
            $constructorArguments['frontendSecurePorts'],
            $constructorArguments['frontendSecureProtocol'],
            $constructorArguments['frontendSslContextOptions'],
        );

        self::assertEquals($host, $frontendWsParametersProvider->getHost());
        self::assertEquals($port, $frontendWsParametersProvider->getPort());
        self::assertEquals($path, $frontendWsParametersProvider->getPath());
        self::assertEquals($transport, $frontendWsParametersProvider->getTransport());
        self::assertEquals($contextOptions, $frontendWsParametersProvider->getContextOptions());
    }

    public function properConfigParametersProvider(): array
    {
        return [
            'host conversion only' => [
                'constructorArguments' => [
                    'innerProvider' => new WebsocketClientParametersProvider('//*:8080'),
                    'frontendSecurePorts' => [],
                    'frontendSecureProtocol' => '',
                    'frontendSslContextOptions' => []
                ],
                'host' => '127.0.0.1',
                'port' => 8080,
                'path' => '',
                'transport' => 'tcp',
                'contextOptions' => []
            ],
            'secure ports to protocol with context options replacement' => [
                'constructorArguments' => [
                    'innerProvider' => new WebsocketClientParametersProvider(
                        'tcp://*:443/ws?context_options[verify_peer] = false'
                    ),
                    'frontendSecurePorts' => ['443'],
                    'frontendSecureProtocol' => 'ssl',
                    'frontendSslContextOptions' => ['allow_self_signed' => true]
                ],
                'host' => '127.0.0.1',
                'port' => 443,
                'path' => 'ws',
                'transport' => 'ssl',
                'contextOptions' => ['allow_self_signed' => true]
            ],
        ];
    }

    public function testGetHostResolvedStar(): void
    {
        $configManager = $this->createMock(ConfigManager::class);
        $configManager->expects(self::once())
            ->method('get')
            ->with('oro_ui.application_url')
            ->willReturn('http://127.0.0.2');

        $frontendWsParametersProvider = new FrontendWebsocketClientParametersProvider(
            new WebsocketClientParametersProvider('//*:8080'),
            [],
            '',
            []
        );
        $frontendWsParametersProvider->setConfigManager($configManager);

        self::assertEquals('127.0.0.2', $frontendWsParametersProvider->getHost());
    }

    /**
     * @dataProvider userAgentDataProvider
     */
    public function testUserAgentParameter(?string $expected, string $websocketClientParameters): void
    {
        $configManager = $this->createMock(ConfigManager::class);
        $configManager
            ->expects(self::never())
            ->method('get');

        $frontendWsParametersProvider = new FrontendWebsocketClientParametersProvider(
            new WebsocketClientParametersProvider($websocketClientParameters),
            [],
            '',
            [],
        );

        self::assertEquals($expected, $frontendWsParametersProvider->getUserAgent());
    }

    public function userAgentDataProvider()
    {
        return [
            'empty user agent' => [
                'expected' => null,
                'websocketClientParameters' => '//*:8080',
            ],
            'empty string user agent' => [
                'expected' => '',
                'websocketClientParameters' => sprintf(
                    '//*:8080?%s',
                    http_build_query(['user_agent' => ''])
                ),
            ],
            'user agent' => [
                'expected' => 'user-agent/5.1.4',
                'websocketClientParameters' => sprintf(
                    '//*:8080?%s',
                    http_build_query(['user_agent' => 'user-agent/5.1.4'])
                ),
            ],
        ];
    }
}
