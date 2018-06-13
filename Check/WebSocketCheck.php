<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Oro\Bundle\SyncBundle\Wamp\TopicPublisher;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Success;
use ZendDiagnostics\Result\Failure;

/**
 * Class for check WebSocket
 */
class WebSocketCheck implements CheckInterface
{
    /** @var array|TopicPublisher[] */
    protected $topicPublishers;

    /**
     * @param array $topicPublishers
     */
    public function __construct(array $topicPublishers)
    {
        foreach ($topicPublishers as $topicPublisher) {
            if (!$topicPublisher instanceof TopicPublisher) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Topic publisher must be instance of "%s", "%s" given.',
                        TopicPublisher::class,
                        is_object($topicPublisher) ? get_class($topicPublisher) : gettype($topicPublisher)
                    )
                );
            }
        }

        $this->topicPublishers = $topicPublishers;
    }

    /**
     * {@inheritdoc}
     */
    public function check(): ResultInterface
    {
        foreach ($this->topicPublishers as $topicPublisher) {
            if (!$topicPublisher->check()) {
                return new Failure('Not available');
            }
        }

        return new Success();
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Check if WebSocket server is available';
    }
}
