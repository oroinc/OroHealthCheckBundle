<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Behat\Context;

use Oro\Bundle\TestFrameworkBundle\Behat\Context\AppKernelAwareInterface;
use Oro\Bundle\TestFrameworkBundle\Behat\Context\AppKernelAwareTrait;
use Oro\Bundle\TestFrameworkBundle\Behat\Context\OroFeatureContext;

class FeatureContext extends OroFeatureContext implements AppKernelAwareInterface
{
    use AppKernelAwareTrait;

    /**
     * @Given I go to healthcheck page
     */
    public function iGoToHealthcheckPage()
    {
        $uri = $this->getAppContainer()->get('router')->generate('oro_default');
        $this->visitPath($uri.'healthcheck');
    }
}
