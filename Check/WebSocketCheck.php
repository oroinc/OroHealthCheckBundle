<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Oro\Bundle\SyncBundle\Wamp\TopicPublisher;
use ZendDiagnostics\Check\CheckInterface;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Skip;
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
        $checkResult = [];
        foreach ($this->topicPublishers as $topicPublisher) {
            if (!$topicPublisher->check()) {
                $checkResult[] = false;
                break;
            }
            $checkResult[] = true;
        }

        // Assumes that first topicPublisher checks backend connection.
        if ($checkResult[0]) {
            // .. and the second topicPublisher checks frontend connection.
            if (isset($checkResult[1]) && $checkResult[1]) {
                return new Success();
            }

            return new Skip('WebSocket backend connection works, but frontend connection cannot be established');
        }

        return new Failure('Not available');
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Check if WebSocket server is available';
    }
}
