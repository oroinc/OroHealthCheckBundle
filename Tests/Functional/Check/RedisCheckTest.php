<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RedisCheckTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testExecuteApiCall()
    {
        $map = [
            'redis_cache' => 'snc_redis.cache',
            'redis_session_storage' => 'snc_redis.session',
            'redis_doctrine_cache' => 'snc_redis.doctrine'
        ];
        $container = static::getContainer();

        foreach ($map as $serviceKey => $serviceName) {
            if (!$container->has($serviceName)) {
                continue;
            }
            $this->client->request(
                'GET',
                $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => $serviceKey])
            );

            $this->assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
        }
    }

    public function testServiceCheck()
    {
        $redisChecks = static::getContainer()->get('oro_health_check.check.redis')->getChecks();

        foreach ($redisChecks as $redisCheck) {
            $this->assertInstanceOf(Success::class, $redisCheck->check());
        }
    }
}
