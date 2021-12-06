<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Elasticsearch\Client;
use Elasticsearch\Connections\Connection;
use Elasticsearch\Connections\ConnectionInterface;
use Elasticsearch\Transport;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Skip;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\ElasticSearchBundle\Client\ClientFactory;
use Oro\Bundle\ElasticSearchBundle\Engine\ElasticSearch as ElasticsearchEngine;
use Oro\Bundle\HealthCheckBundle\Check\ElasticsearchCheck;

class ElasticsearchCheckTest extends \PHPUnit\Framework\TestCase
{
    private const ENGINE_PARAMETERS = ['client' => ['name' => 'test_client']];

    /** @var ClientFactory|\PHPUnit\Framework\MockObject\MockObject */
    private $clientFactory;

    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(ClientFactory::class);
    }

    /**
     * @dataProvider checkDataProvider
     */
    public function testCheckConfigured(bool $ping, bool $isAlive, ResultInterface $expected)
    {
        $connection = $this->createMock(Connection::class);
        $connection->expects($this->any())
            ->method('ping')
            ->willReturn($ping);
        $connection->expects($this->any())
            ->method('isAlive')
            ->willReturn($isAlive);

        $this->setUpClient($connection);

        $this->assertEquals($expected, $this->getCheck(ElasticSearchEngine::ENGINE_NAME)->check());
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

    public function testCheckConfiguredWithUnsupportedConnection()
    {
        $connection = $this->createMock(ConnectionInterface::class);
        $connection->expects($this->never())
            ->method('isAlive');

        $this->setUpClient($connection);

        $this->assertEquals(
            new Skip('Elasticsearch connection does not support ping. Check Skipped.'),
            $this->getCheck(ElasticSearchEngine::ENGINE_NAME)->check()
        );
    }

    public function testCheckNotConfigured()
    {
        $this->clientFactory->expects($this->never())
            ->method('create');

        $this->assertEquals(
            new Skip('Elasticsearch connection is not configured. Check Skipped.'),
            $this->getCheck('orm')->check()
        );
    }

    public function testGetLabel()
    {
        $this->assertEquals(
            'Check if Elasticsearch is available in case it is configured',
            $this->getCheck('')->getLabel()
        );
    }

    private function setUpClient(ConnectionInterface $connection): void
    {
        $transport = $this->createMock(Transport::class);
        $transport->expects($this->once())
            ->method('getConnection')
            ->willReturn($connection);

        $client = new Client(
            $transport,
            function ($name) {
                return $name;
            },
            []
        );

        $this->clientFactory->expects($this->once())
            ->method('create')
            ->with(self::ENGINE_PARAMETERS['client'])
            ->willReturn($client);
    }

    private function getCheck(string $engineName): ElasticsearchCheck
    {
        return new ElasticsearchCheck($this->clientFactory, $engineName, self::ENGINE_PARAMETERS);
    }
}
