<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckCollectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

/**
 * Class for check redis availability in case if it is configured
 */
class RedisCheckCollection implements CheckCollectionInterface
{
    /** @var array */
    protected $clients;

    /**
     * @param ContainerInterface $container
     * @param array $clients
     */
    public function __construct(array $clients)
    {
        $this->clients = array_filter($clients);
    }

    /**
     * {@inheritdoc}
     */
    public function getChecks(): array
    {
        $checks = [];
        foreach ($this->clients as $label => $client) {
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
