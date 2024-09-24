<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Symfony\Component\HttpFoundation\Response;

class RedisCheckTest extends WebTestCase
{
    private array $serviceMap = [
        'redis_cache' => 'oro.cache.redis_provider',
        'redis_session_storage' => 'oro_redis_config.session.redis_connection',
        'redis_doctrine_cache' => 'oro.cache.doctrine.redis_provider'
    ];

    #[\Override]
    protected function setUp(): void
    {
        $this->initClient([], $this->generateBasicAuthHeader());
    }

    public function testExecuteApiCall(): void
    {
        if (!$this->isRedisConfigured()) {
            return;
        }

        $container = self::getContainer();

        $this->client->request(
            'GET',
            $this->getUrl('liip_monitor_list_checks')
        );

        $listChecks = json_decode(
            $this->client->getResponse()->getContent(),
            JSON_OBJECT_AS_ARRAY
        );

        foreach ($this->serviceMap as $checkId => $serviceName) {
            self::assertContains(
                $checkId,
                $listChecks,
                sprintf('Monitor health check checkId %s does not exists', $checkId)
            );

            self::assertTrue(
                $container->has($serviceName),
                sprintf('Service %s does not exists', $serviceName)
            );

            $this->client->request(
                'GET',
                $this->getUrl('liip_monitor_run_single_check_http_status', ['checkId' => $checkId])
            );

            self::assertResponseStatusCodeEquals($this->client->getResponse(), Response::HTTP_OK);
        }
    }

    public function testSuccessServiceCheck(): void
    {
        if (!$this->isRedisConfigured()) {
            return;
        }

        $redisChecks = self::getContainer()->get('oro_health_check.check.redis')->getChecks();

        foreach ($redisChecks as $redisCheck) {
            $this->assertInstanceOf(Success::class, $redisCheck->check());
        }
    }

    public function testFailureServiceCheck(): void
    {
        if (!$this->isRedisConfigured()) {
            $redisChecks = self::getContainer()->get('oro_health_check.check.redis')->getChecks();

            foreach ($redisChecks as $redisCheck) {
                $this->assertInstanceOf(Failure::class, $redisCheck->check());
            }
        }
    }

    private function isRedisConfigured(): bool
    {
        $container = self::getContainer();

        return $container->hasParameter('redis_dsn_cache') &&
            $container->hasParameter('redis_dsn_doctrine') &&
            $container->hasParameter('session_handler_dsn');
    }
}
