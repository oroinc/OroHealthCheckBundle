<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Oro\Component\AmqpMessageQueue\Provider\TransportConnectionConfigProvider;
use Oro\Component\AmqpMessageQueue\Transport\Amqp\AmqpConnection;

/**
 * Class for check RabbitMQ availability
 */
class RabbitMQCheck implements CheckInterface
{
    protected ?TransportConnectionConfigProvider $configProvider;

    public function __construct(TransportConnectionConfigProvider $configProvider = null)
    {
        $this->configProvider = $configProvider;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        if ($this->isConfigured()) {
            $connection = AmqpConnection::createFromConfig($this->configProvider);
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
        $configuration = $this->configProvider?->getConfiguration();
        return isset(
            $configuration['host'],
            $configuration['port'],
            $configuration['user'],
            $configuration['password'],
            $configuration['vhost']
        );
    }
}
