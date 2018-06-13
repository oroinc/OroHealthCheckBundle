<?php

namespace Oro\Bundle\HealthCheckBundle;

use Oro\Bundle\HealthCheckBundle\DependencyInjection\OroHealthCheckExtension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class OroHealthCheckBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension()
    {
        if (!$this->extension) {
            $this->extension = new OroHealthCheckExtension();
        }

        return $this->extension;
    }
}
