<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Liip\MonitorBundle\Check\DoctrineDbal;

class DoctrineDBALCheck extends DoctrineDbal
{
    /**
     * {@inheritdoc}
     */
    public function getLabel()
    {
        return 'Check if Doctrine DBAL is available';
    }
}
