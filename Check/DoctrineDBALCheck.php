<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Liip\MonitorBundle\Check\DoctrineDbal;

/**
 * Health check for Doctrine DBAL availability.
 */
class DoctrineDBALCheck extends DoctrineDbal
{
    #[\Override]
    public function getLabel()
    {
        return 'Check if Doctrine DBAL is available';
    }
}
