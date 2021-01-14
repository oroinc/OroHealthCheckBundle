<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Doctrine\Persistence\ManagerRegistry;
use Oro\Bundle\HealthCheckBundle\Check\DoctrineDBALCheck;

class DoctrineDBALCheckTest extends \PHPUnit\Framework\TestCase
{
    /** @var DoctrineDbalCheck */
    protected $check;

    protected function setUp(): void
    {
        /** @var ManagerRegistry $doctrine */
        $doctrine = $this->createMock(ManagerRegistry::class);

        $this->check = new DoctrineDBALCheck($doctrine);
    }

    public function testGetLabel()
    {
        $this->assertEquals('Check if Doctrine DBAL is available', $this->check->getLabel());
    }
}
