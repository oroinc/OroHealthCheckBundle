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

    private string $host;

    private string $transport;

    private array $contextOptions;

    private ?ConfigManager $configManager = null;

    public function __construct(
        WebsocketClientParametersProvider $clientParametersProvider,
        array $frontendSecurePorts,
        string $frontendSecureProtocol,
        array $frontendSslContextOptions
    ) {
        $this->clientParametersProvider = $clientParametersProvider;

        $this->host = $clientParametersProvider->getHost();

        $this->transport = in_array($clientParametersProvider->getPort(), $frontendSecurePorts)
            ? $frontendSecureProtocol
            : 'tcp';
        $this->contextOptions = $frontendSslContextOptions;
    }

    public function setConfigManager(ConfigManager $configManager): void
    {
        $this->configManager = $configManager;
    }

    public function getHost(): string
    {
        $host = $this->host === '*' ? '127.0.0.1' : $this->host;

        if ($this->configManager && $this->host === '*') {
            $appUrl = $this->configManager->get('oro_ui.application_url');
            $parsedAppUrl = parse_url($appUrl);
            $host = $parsedAppUrl['host'];
        }

        return $host;
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

    public function getUserAgent(): ?string
    {
        return $this->clientParametersProvider->getUserAgent();
    }
}
