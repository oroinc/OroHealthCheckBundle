<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check\WebSocket;

use Oro\Bundle\HealthCheckBundle\Check\WebSocket\FrontendClientAttributesFactory;
use Oro\Bundle\SyncBundle\Client\Wamp\Factory\ClientAttributes;

class FrontendClientAttributesFactoryTest extends \PHPUnit\Framework\TestCase
{
    /**
     * @dataProvider createClientAttributesDataProvider
     *
     * @param string $secureTransport
     * @param array $securePorts
     * @param array $sslContextOptions
     * @param string $host
     * @param string $port
     * @param string $path
     * @param ClientAttributes $expectedClientAttributes
     */
    public function testCreateClientAttributes(
        string $secureTransport,
        array $securePorts,
        array $sslContextOptions,
        string $host,
        string $port,
        string $path,
        $expectedClientAttributes
    ): void {
        $factory = $this->createFactory($secureTransport, $securePorts, $sslContextOptions);

        $clientAttributes = $factory->createClientAttributes($host, $port, $path);

        self::assertEquals($expectedClientAttributes, $clientAttributes);
    }

    /**
     * @return array
     */
    public function createClientAttributesDataProvider(): array
    {
        return [
            'not secure connection' => [
                'secureTransport' => 'tls',
                'securePorts' => [443],
                'sslContextOptions' => [],
                'host' => 'localhost',
                'port' => 8080,
                'path' => '',
                'expectedClientAttributes' => new ClientAttributes('localhost', '8080', '', 'tcp', [])
            ],
            'secure connection' => [
                'secureTransport' => 'tls',
                'securePorts' => [443],
                'sslContextOptions' => [],
                'host' => 'localhost',
                'port' => 443,
                'path' => 'ws',
                'expectedClientAttributes' => new ClientAttributes('localhost', '443', 'ws', 'tls', [])
            ],
            'secure connection with multiple secure ports' => [
                'secureTransport' => 'tls',
                'securePorts' => [443, 8081],
                'sslContextOptions' => [],
                'host' => 'localhost',
                'port' => 8081,
                'path' => 'ws',
                'expectedClientAttributes' => new ClientAttributes('localhost', '8081', 'ws', 'tls', [])
            ],
            'secure connection with custom secure transport' => [
                'secureTransport' => 'ssl',
                'securePorts' => [8081],
                'sslContextOptions' => [],
                'host' => 'localhost',
                'port' => 8081,
                'path' => 'ws',
                'expectedClientAttributes' => new ClientAttributes('localhost', '8081', 'ws', 'ssl', [])
            ],
            'secure connection with custom ssl context options' => [
                'secureTransport' => 'tls',
                'securePorts' => [443],
                'sslContextOptions' => ['sampleContextOption' => 'sampleContextValue'],
                'host' => 'localhost',
                'port' => 443,
                'path' => 'ws',
                'expectedClientAttributes' => new ClientAttributes(
                    'localhost',
                    '443',
                    'ws',
                    'tls',
                    ['sampleContextOption' => 'sampleContextValue']
                )
            ],
            'secure connection with empty custom options' => [
                'secureTransport' => '',
                'securePorts' => [],
                'sslContextOptions' => [],
                'host' => 'localhost',
                'port' => 8080,
                'path' => '',
                'expectedClientAttributes' => new ClientAttributes('localhost', '8080', '', 'tcp', [])
            ],
        ];
    }

    /**
     * @param string $secureTransport
     * @param array $securePorts
     * @param array $sslContextOptions
     *
     * @return FrontendClientAttributesFactory
     */
    private function createFactory(
        string $secureTransport,
        array $securePorts,
        array $sslContextOptions
    ): FrontendClientAttributesFactory {
        return new FrontendClientAttributesFactory($secureTransport, $securePorts, $sslContextOptions);
    }
}
