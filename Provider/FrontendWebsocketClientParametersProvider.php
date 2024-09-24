<?php

namespace Oro\Bundle\HealthCheckBundle\Provider;

use Oro\Bundle\ConfigBundle\Config\ConfigManager;
use Oro\Bundle\SyncBundle\Provider\WebsocketClientParametersProvider;
use Oro\Bundle\SyncBundle\Provider\WebsocketClientParametersProviderInterface;

/**
 * Websocket client connection parameters provider to perform server health check.
 */
class FrontendWebsocketClientParametersProvider implements WebsocketClientParametersProviderInterface
{
    private WebsocketClientParametersProvider $clientParametersProvider;

    private ConfigManager $configManager;

    private string $host;

    private string $transport;

    private array $contextOptions;

    public function __construct(
        WebsocketClientParametersProvider $clientParametersProvider,
        ConfigManager $configManager,
        array $frontendSecurePorts,
        string $frontendSecureProtocol,
        array $frontendSslContextOptions
    ) {
        $this->clientParametersProvider = $clientParametersProvider;
        $this->configManager = $configManager;

        $this->host = $clientParametersProvider->getHost();

        $this->transport = in_array($clientParametersProvider->getPort(), $frontendSecurePorts)
            ? $frontendSecureProtocol
            : 'tcp';
        $this->contextOptions = $frontendSslContextOptions;
    }

    #[\Override]
    public function getHost(): string
    {
        $host = $this->host;

        if ($host === '*') {
            $appUrl = $this->configManager->get('oro_ui.application_url');
            $parsedAppUrl = parse_url($appUrl);
            $host = $parsedAppUrl['host'];
        }

        return $host;
    }

    #[\Override]
    public function getPort(): int
    {
        return $this->clientParametersProvider->getPort();
    }

    #[\Override]
    public function getPath(): string
    {
        return $this->clientParametersProvider->getPath();
    }

    #[\Override]
    public function getTransport(): string
    {
        return $this->transport;
    }

    #[\Override]
    public function getContextOptions(): array
    {
        return $this->contextOptions;
    }

    #[\Override]
    public function getUserAgent(): ?string
    {
        return $this->clientParametersProvider->getUserAgent();
    }
}
