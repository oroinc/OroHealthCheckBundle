<?php

namespace Oro\Bundle\HealthCheckBundle\Provider;

use Oro\Bundle\SyncBundle\Provider\WebsocketClientParametersProvider;
use Oro\Bundle\SyncBundle\Provider\WebsocketClientParametersProviderInterface;

/**
 * Websocket client connection parameters provider to perform server health check.
 */
class FrontendWebsocketClientParametersProvider implements WebsocketClientParametersProviderInterface
{
    private WebsocketClientParametersProvider $clientParametersProvider;

    private string $host;

    private string $transport;

    private array $contextOptions;

    public function __construct(
        WebsocketClientParametersProvider $clientParametersProvider,
        array $frontendSecurePorts,
        string $frontendSecureProtocol,
        array $frontendSslContextOptions
    ) {
        $this->clientParametersProvider = $clientParametersProvider;

        $host = $clientParametersProvider->getHost();
        if ($host === '*') {
            $host = '127.0.0.1';
        }
        $this->host = $host;

        $this->transport = in_array($clientParametersProvider->getPort(), $frontendSecurePorts)
            ? $frontendSecureProtocol
            : 'tcp';
        $this->contextOptions = $frontendSslContextOptions;
    }

    public function getHost(): string
    {
        return $this->host;
    }

    public function getPort(): int
    {
        return $this->clientParametersProvider->getPort();
    }

    public function getPath(): string
    {
        return $this->clientParametersProvider->getPath();
    }

    public function getTransport(): string
    {
        return $this->transport;
    }

    public function getContextOptions(): array
    {
        return $this->contextOptions;
    }
}
