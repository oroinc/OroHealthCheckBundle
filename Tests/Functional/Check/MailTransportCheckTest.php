<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Functional\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Oro\Bundle\HealthCheckBundle\Check\MailTransportCheck;
use Oro\Bundle\TestFrameworkBundle\Test\WebTestCase;
use Oro\Component\Testing\ClassExtensionTrait;

/**
 * We cannot check a connection because we don't have smtp server in test environment.
 */
class MailTransportCheckTest extends WebTestCase
{
    use ClassExtensionTrait;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->initClient([], self::generateBasicAuthHeader());
    }

    public function testServiceExists(): void
    {
        $serviceId = 'oro_health_check.check.mail_transport';

        self::assertTrue(self::getContainer()->has($serviceId));
        self::assertInstanceOf(MailTransportCheck::class, self::getContainer()->get($serviceId));
        $this->assertClassImplements(CheckInterface::class, MailTransportCheck::class);
    }
}
