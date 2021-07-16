<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\HealthCheckBundle\Check\MaintenanceModeCheck;
use Oro\Bundle\MaintenanceBundle\Drivers\AbstractDriver;
use Oro\Bundle\MaintenanceBundle\Drivers\DriverFactory;
use Oro\Bundle\MaintenanceBundle\Drivers\FileDriver;

class MaintenanceModeCheckTest extends \PHPUnit\Framework\TestCase
{
    /** @var DriverFactory|\PHPUnit\Framework\MockObject\MockObject */
    protected $driverFactory;

    /** @var MaintenanceModeCheck */
    protected $check;

    protected function setUp(): void
    {
        $this->driverFactory = $this->createMock(DriverFactory::class);

        $this->check = new MaintenanceModeCheck($this->driverFactory);
    }

    /**
     * @dataProvider checkWithNoTtlDriverProvider
     *
     * @param bool $decide
     * @param ResultInterface $expected
     */
    public function testCheckWithNoTtlDriver(bool $decide, ResultInterface $expected)
    {
        /** @var AbstractDriver|\PHPUnit\Framework\MockObject\MockObject $driver */
        $driver = $this->createMock(AbstractDriver::class);
        $driver->expects($this->once())
            ->method('decide')
            ->willReturn($decide);

        $this->driverFactory->expects($this->once())
            ->method('getDriver')
            ->willReturn($driver);

        $this->assertEquals($expected, $this->check->check());
    }

    /**
     * @return array
     */
    public function checkWithNoTtlDriverProvider()
    {
        return [
            [
                'decide' => false,
                'expected' => new Success('Off')
            ],
            [
                'decide' => true,
                'expected' => new Success('On')
            ],
        ];
    }

    /**
     * @dataProvider checkWithTtlDriverProvider
     *
     * @param bool $decide
     * @param bool $isExpired
     * @param ResultInterface $expected
     */
    public function testCheckWithTtlDriver(bool $decide, bool $isExpired, ResultInterface $expected)
    {
        /** @var FileDriver|\PHPUnit\Framework\MockObject\MockObject $driver */
        $driver = $this->createMock(FileDriver::class);
        $driver->expects($this->once())
            ->method('decide')
            ->willReturn($decide);

        $driver->expects($this->any())
            ->method('isExpired')
            ->willReturn($isExpired);

        $this->driverFactory->expects($this->once())
            ->method('getDriver')
            ->willReturn($driver);

        $this->assertEquals($expected, $this->check->check());
    }

    /**
     * @return array
     */
    public function checkWithTtlDriverProvider()
    {
        return [
            [
                'decide' => false,
                'isExpired' => false,
                'expected' => new Success('Off')
            ],
            [
                'decide' => true,
                'isExpired' => false,
                'expected' => new Success('On')
            ],
            [
                'decide' => false,
                'isExpired' => true,
                'expected' => new Success('Off')
            ],
            [
                'decide' => true,
                'isExpired' => true,
                'expected' => new Failure('Expired')
            ],
        ];
    }

    public function testGetLabel()
    {
        $this->assertEquals('Check if Maintenance Mode is running and not expired', $this->check->getLabel());
    }
}
