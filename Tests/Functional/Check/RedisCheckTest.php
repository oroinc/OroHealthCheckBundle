<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Predis\Client;
use Predis\Connection\ConnectionException;
use Symfony\Component\HttpFoundation\Response;
use ZendDiagnostics\Result\Success;

class RedisCheckTest extends WebTestCase
{
    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $this->initClient([], $this->generateBasicAuthHeader());

        if (!$this->isApplicable()) {
            $this->markTestSkipped('Redis environment is not configured properly.');
        }
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

    /**
     * @return bool
     */
    private function isApplicable(): bool
    {
        $container = static::getContainer();
        if (!$container->has('snc_redis.cache')) {
            return false;
        }

        try {
            /** @var Client $client */
            $client = $container->get('snc_redis.cache');
            $client->ping();
        } catch (ConnectionException $e) {
            return false;
        }

        return true;
    }
}
