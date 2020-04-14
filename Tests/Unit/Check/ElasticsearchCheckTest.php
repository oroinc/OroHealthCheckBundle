<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Elasticsearch\Client;
use Elasticsearch\Connections\Connection;
use Elasticsearch\Connections\ConnectionInterface;
use Elasticsearch\Transport;
use Oro\Bundle\ElasticSearchBundle\Client\ClientFactory;
use Oro\Bundle\ElasticSearchBundle\Engine\ElasticSearch as ElasticsearchEngine;
use Oro\Bundle\HealthCheckBundle\Check\ElasticsearchCheck;
use ZendDiagnostics\Result\Failure;
use ZendDiagnostics\Result\ResultInterface;
use ZendDiagnostics\Result\Skip;
use ZendDiagnostics\Result\Success;

class ElasticsearchCheckTest extends \PHPUnit\Framework\TestCase
{
    const ENGINE_PARAMETERS = ['client' => ['name' => 'test_client']];

    /** @var ClientFactory|\PHPUnit\Framework\MockObject\MockObject */
    protected $clientFactory;

    protected function setUp(): void
    {
        $this->clientFactory = $this->createMock(ClientFactory::class);
    }

    /**
     * @dataProvider checkDataProvider
     *
     * @param bool $ping
     * @param string $isAlive
     * @param ResultInterface $expected
     */
    public function testCheckConfigured(bool $ping, string $isAlive, ResultInterface $expected)
    {
        /** @var Connection|\PHPUnit\Framework\MockObject\MockObject $connection */
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

    /**
     * @return array
     */
    public function checkDataProvider()
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
        /** @var ConnectionInterface|\PHPUnit\Framework\MockObject\MockObject $connection */
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

    /**
     * @param ConnectionInterface $connection
     */
    protected function setUpClient(ConnectionInterface $connection)
    {
        /** @var Transport|\PHPUnit\Framework\MockObject\MockObject $transport */
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

    /**
     * @param string $engineName
     * @return ElasticsearchCheck
     */
    protected function getCheck($engineName)
    {
        return new ElasticsearchCheck($this->clientFactory, $engineName, self::ENGINE_PARAMETERS);
    }
}
