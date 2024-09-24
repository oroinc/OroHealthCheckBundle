<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Elastic\Elasticsearch\Client;
use Elastic\Transport\NodePool\Node;
use Elastic\Transport\NodePool\NodePoolInterface;
use Elastic\Transport\NodePool\Resurrect\ResurrectInterface;
use Elastic\Transport\Transport;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\ElasticSearchBundle\Client\ClientFactory;
use Oro\Bundle\ElasticSearchBundle\Engine\ElasticSearch as ElasticsearchEngine;
use Oro\Bundle\HealthCheckBundle\Check\ElasticsearchCheck;
use Psr\Http\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class ElasticsearchCheckTest extends \PHPUnit\Framework\TestCase
{
    private const ENGINE_PARAMETERS = ['client' => ['name' => 'test_client']];

    /** @var ClientFactory|\PHPUnit\Framework\MockObject\MockObject */
    private $clientFactory;

    #[\Override]
    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(ClientFactory::class);
    }

    /**
     * @dataProvider checkDataProvider
     */
    public function testCheckConfigured(bool $ping, bool $isAlive, ResultInterface $expected)
    {
        $resurrect = $this->createMock(ResurrectInterface::class);
        $resurrect->expects($this->any())
            ->method('ping')
            ->willReturn($ping);

        $node = new Node('http://localhost');
        $node->markAlive($isAlive);

        $this->setUpClient($node);

        $this->assertEquals($expected, $this->getCheck($resurrect, ElasticSearchEngine::ENGINE_NAME)->check());
    }

    public function checkDataProvider(): array
    {
        return [
            [
                'ping' => true,
                'isAlive' => true,
                'expected' => new Success()
            ],
            [
                'ping' => false,
                'isAlive' => true,
                'expected' => new Failure()
            ],
            [
                'ping' => true,
                'isAlive' => false,
                'expected' => new Failure()
            ],
            [
                'ping' => false,
                'isAlive' => false,
                'expected' => new Failure()
            ]
        ];
    }

    public function testCheckNotConfigured()
    {
        $this->clientFactory->expects($this->never())
            ->method('create');

        $this->assertEquals(
            new Skip('Elasticsearch connection is not configured. Check Skipped.'),
            $this->getCheck($this->createMock(ResurrectInterface::class), 'orm')->check()
        );
    }

    public function testGetLabel()
    {
        $this->assertEquals(
            'Check if Elasticsearch is available in case it is configured',
            $this->getCheck($this->createMock(ResurrectInterface::class), '')->getLabel()
        );
    }

    private function setUpClient(Node $node): void
    {
        $nodePool = $this->createMock(NodePoolInterface::class);
        $nodePool->expects($this->any())
            ->method('nextNode')
            ->willReturn($node);

        $transport = new Transport(
            $this->createMock(ClientInterface::class),
            $nodePool,
            $this->createMock(LoggerInterface::class)
        );

        $client = new Client(
            $transport,
            $this->createMock(LoggerInterface::class)
        );

        $this->clientFactory->expects($this->once())
            ->method('create')
            ->with(self::ENGINE_PARAMETERS['client'])
            ->willReturn($client);
    }

    private function getCheck(ResurrectInterface $resurrect, string $engineName): ElasticsearchCheck
    {
        return new ElasticsearchCheck($this->clientFactory, $resurrect, $engineName, self::ENGINE_PARAMETERS);
    }
}
