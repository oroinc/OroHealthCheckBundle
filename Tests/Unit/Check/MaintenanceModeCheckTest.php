<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\HealthCheckBundle\Check\MaintenanceModeCheck;
use Oro\Bundle\MaintenanceBundle\Drivers\AbstractDriver;
use Oro\Bundle\MaintenanceBundle\Drivers\DriverFactory;

class MaintenanceModeCheckTest extends \PHPUnit\Framework\TestCase
{
    private DriverFactory|\PHPUnit\Framework\MockObject\MockObject $driverFactory;

    private MaintenanceModeCheck $check;

    #[\Override]
    protected function setUp(): void
    {
        $this->driverFactory = $this->createMock(DriverFactory::class);

        $this->check = new MaintenanceModeCheck($this->driverFactory);
    }

    /**
     * @dataProvider checkProvider
     */
    public function testCheck(bool $decide, ResultInterface $expected): void
    {
        /** @var AbstractDriver|\PHPUnit\Framework\MockObject\MockObject $driver */
        $driver = $this->createMock(AbstractDriver::class);
        $driver->expects(self::once())
            ->method('decide')
            ->willReturn($decide);

        $this->driverFactory->expects(self::once())
            ->method('getDriver')
            ->willReturn($driver);

        self::assertEquals($expected, $this->check->check());
    }

    public function checkProvider(): array
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

    public function testGetLabel(): void
    {
        self::assertEquals('Check if Maintenance Mode is running', $this->check->getLabel());
    }
}
