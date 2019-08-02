<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\FileStorageCheckCollection;
use ZendDiagnostics\Check\DirWritable;

class FileStorageCheckCollectionTest extends \PHPUnit\Framework\TestCase
{
    public function testGetChecks()
    {
        $collection = new FileStorageCheckCollection(
            __DIR__,
            ['first_key' => __DIR__ . '/../dir/path/test', 'second_key' => '/some/other/dir']
        );

        $check1 = new DirWritable(__DIR__ . '/../dir/path/test');
        $check1->setLabel(sprintf('Check if "%s" is writable', __DIR__ . '/../dir/path/test'));

        $check2 = new DirWritable('/some/other/dir');
        $check2->setLabel('Check if "/some/other/dir" is writable');

        $this->assertEquals(
            ['first_key' => $check1, 'second_key' => $check2],
            $collection->getChecks()
        );
    }
}
