<?php

namespace Oro\Bundle\HealthCheckBundle\Check\WebSocket;

use Oro\Bundle\SyncBundle\Client\Wamp\Factory\ClientAttributes;

/**
 * Creates ClientAttributes for using in frontend websocket connection check.
 * - guesses websocket transport: uses secureTransport if port is in securePorts
 */
class FrontendClientAttributesFactory
{
    /**
     * @var string
     */
    private $secureTransport;

    /**
     * @var string[]|int[]
     */
    private $securePorts;

    /**
     * @var array
     */
    private $sslContextOptions;

    /**
     * @param string $secureTransport This transport will be used if websocket port is in $securePorts
     * @param array $securePorts List of secure ports to compare websocket port with
     * @param array $sslContextOptions SSL context options which will be passed to ClientAttributes
     */
    public function __construct(string $secureTransport, array $securePorts, array $sslContextOptions)
    {
        $this->secureTransport = $secureTransport;
        $this->securePorts = $securePorts;
        $this->sslContextOptions = $sslContextOptions;
    }

    /**
     * @param string $host
     * @param int $port
     * @param string $path
     *
     * @return ClientAttributes
     */
    public function createClientAttributes(string $host, int $port, string $path): ClientAttributes
    {
        return new ClientAttributes($host, $port, $path, $this->guessTransport($port), $this->sslContextOptions);
    }

    /**
     * @param int $port
     *
     * @return string
     */
    protected function guessTransport(int $port): string
    {
        if (\in_array($port, $this->securePorts, false)) {
            return $this->secureTransport;
        }

        return 'tcp';
    }
}
