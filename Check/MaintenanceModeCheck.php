<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Oro\Bundle\MaintenanceBundle\Drivers\DriverFactory;

/**
 * Class for check Maintenance Mode
 */
class MaintenanceModeCheck implements CheckInterface
{
    protected DriverFactory $driverFactory;

    public function __construct(DriverFactory $factory)
    {
        $this->driverFactory = $factory;
    }

    /**
     * @return Success
     */
    #[\Override]
    public function check(): ResultInterface
    {
        $driver = $this->driverFactory->getDriver();

        return !$driver->decide() ? new Success('Off') : new Success('On');
    }

    #[\Override]
    public function getLabel(): string
    {
        return 'Check if Maintenance Mode is running';
    }
}
