<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\RedisCheck;
use Oro\Bundle\HealthCheckBundle\Check\RedisCheckCollection;
use Predis\Client;

class RedisCheckCollectionTest extends \PHPUnit\Framework\TestCase
{
    /** @var RedisCheckCollection */
    protected $check;

    protected function setUp(): void
    {
        $this->check = new RedisCheckCollection(
            [
                'Redis cache' => null,
                'Redis doctrine cache'=> new Client(),
                'Redis session cache' => null,
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
