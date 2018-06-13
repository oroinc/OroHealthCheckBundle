<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Symfony\Component\DependencyInjection\ContainerInterface;
use ZendDiagnostics\Check\CheckCollectionInterface;

/**
 * Class for check redis availability in case if it is configured
 */
class RedisCheckCollection implements CheckCollectionInterface
{
    /** @var ContainerInterface */
    protected $container;
    
    /** @var array */
    protected $clients;

    /**
     * @param ContainerInterface $container
     * @param array $clients
     */
    public function __construct(ContainerInterface $container, array $clients)
    {
        $this->container = $container;
        $this->clients = $clients;
    }
    
    /**
     * @return array
     */
    protected function getClients(): array
    {
        $clients = array_map(
            function (string $client) {
                return $this->container->get($client, ContainerInterface::NULL_ON_INVALID_REFERENCE);
            },
            $this->clients
        );

        return array_filter($clients);
    }

    /**
     * {@inheritdoc}
     */
    public function getChecks(): array
    {
        $checks = [];
        foreach ($this->getClients() as $label => $client) {
            $checks[$this->getCheckId($label)] = new RedisCheck($client, $label);
        }

        return $checks;
    }

    /**
     * @param string $label
     * @return string
     */
    private function getCheckId(string $label): string
    {
        return strtolower(str_replace(' ', '_', $label));
    }
}
