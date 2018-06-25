<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\RedisCheck;
use Oro\Bundle\HealthCheckBundle\Check\RedisCheckCollection;
use Predis\Client;
use Symfony\Component\DependencyInjection\ContainerInterface;

class RedisCheckCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var ContainerInterface|\PHPUnit\Framework\MockObject\MockObject */
    protected $container;

    /** @var RedisCheckCollection */
    protected $check;

    protected function setUp()
    {
        $this->container = $this->createMock(ContainerInterface::class);
        $this->container->expects($this->any())
            ->method('get')
            ->willReturnMap(
                [
                    ['snc_redis.cache', ContainerInterface::NULL_ON_INVALID_REFERENCE, null],
                    ['snc_redis.doctrine', ContainerInterface::NULL_ON_INVALID_REFERENCE, new Client()],
                    ['snc_redis.session', ContainerInterface::NULL_ON_INVALID_REFERENCE, null],
                ]
            );

        $this->check = new RedisCheckCollection(
            $this->container,
            [
                'Redis cache' => 'snc_redis.cache',
                'Redis doctrine cache'=> 'snc_redis.doctrine',
                'Redis session cache' => 'snc_redis.session',
            ]
        );
    }

    public function testGetChecks()
    {
        $this->assertEquals(
            ['redis_doctrine_cache' => new RedisCheck(new Client(), 'Redis doctrine cache')],
            $this->check->getChecks()
        );
    }
}
