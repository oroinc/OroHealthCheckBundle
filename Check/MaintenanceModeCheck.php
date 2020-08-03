<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckInterface;
use Laminas\Diagnostics\Result\Failure;
use Laminas\Diagnostics\Result\ResultInterface;
use Laminas\Diagnostics\Result\Success;
use Lexik\Bundle\MaintenanceBundle\Drivers\DriverFactory;
use Oro\Bundle\HealthCheckBundle\Drivers\FileDriver;

/**
 * Class for check Maintenance Mode
 */
class MaintenanceModeCheck implements CheckInterface
{
    /** @var DriverFactory */
    protected $driverFactory;

    /**
     * @param DriverFactory $factory
     */
    public function __construct(DriverFactory $factory)
    {
        $this->driverFactory = $factory;
    }

    /**
     * @return Failure|Success
     */
    public function check(): ResultInterface
    {
        $driver = $this->driverFactory->getDriver();

        if (!$driver->decide()) {
            return new Success('Off');
        }

        return $driver instanceof FileDriver && $driver->isExpired() ? new Failure('Expired') : new Success('On');
    }

    /**
     * {@inheritdoc}
     */
    public function getLabel(): string
    {
        return 'Check if Maintenance Mode is running and not expired';
    }
}
