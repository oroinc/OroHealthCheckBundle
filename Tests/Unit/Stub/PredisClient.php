<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Stub;

use Predis\Client;

class PredisClient extends Client
{
    public function ping()
    {
    }
}
