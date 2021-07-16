<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Oro\Component\AmqpMessageQueue\Transport\Amqp\AmqpConnection;

/**
 * Class for check RabbitMQ availability
 */
class RabbitMQCheck implements CheckInterface
{
    /** @var array|null */
    protected $config;

    public function __construct(array $config = null)
    {
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        if ($this->isConfigured()) {
            $connection = AmqpConnection::createFromConfig([
                'host' => $this->config['host'],
                'port' => $this->config['port'],
                'user' => $this->config['user'],
                'password' => $this->config['password'],
                'vhost' => $this->config['vhost'],
            ]);
            $connection->createSession()->createProducer();

            return new Success();
        }

        return new Skip('RabbitMQ connection is not configured. Check Skipped.');
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Check if RabbitMQ is available in case it is configured';
    }

    protected function isConfigured(): bool
    {
        return is_array($this->config) &&
            isset(
                $this->config['host'],
                $this->config['port'],
                $this->config['user'],
                $this->config['password'],
                $this->config['vhost']
            );
    }
}
