<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Laminas\Diagnostics\Check\DirWritable;
use Oro\Bundle\HealthCheckBundle\Check\FileStorageCheckCollection;
use PHPUnit\Framework\TestCase;

class FileStorageCheckCollectionTest extends TestCase
{
    public function testGetChecks(): void
    {
        $collection = new FileStorageCheckCollection(
            __DIR__,
            [
                'first_key' => [
                    'dir' => __DIR__ . '/dir/path/test',
                    'title' => 'path/test',
                ],
                'second_key' => [
                    'dir' => '/some/other/dir',
                    'title' => 'other_dir',
                ]
            ]
        );

        $check1 = new DirWritable(__DIR__ . '/dir/path/test');
        $check1->setLabel(sprintf('Check if "%s" directory is writable', 'path/test'));

        $check2 = new DirWritable('/some/other/dir');
        $check2->setLabel('Check if "other_dir" directory is writable');

        $this->assertEquals(
            ['first_key' => $check1, 'second_key' => $check2],
            $collection->getChecks()
        );
    }
}
