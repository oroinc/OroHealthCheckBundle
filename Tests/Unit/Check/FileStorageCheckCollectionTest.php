<?php

namespace Oro\Bundle\HealthCheckBundle\Tests\Unit\Check;

use Oro\Bundle\HealthCheckBundle\Check\FileStorageCheckCollection;
use ZendDiagnostics\Check\DirWritable;

class FileStorageCheckCollectionTest extends \PHPUnit_Framework_TestCase
{
    /** @var FileStorageCheckCollection */
    protected $check;

    protected function setUp()
    {
        $this->check = new FileStorageCheckCollection(__DIR__, [__DIR__ . '/../dir/path/test', '/some/other/dir']);
    }

    public function testGetChecks()
    {
        $check1 = new DirWritable(__DIR__ . '/../dir/path/test');
        $check1->setLabel(sprintf('Check if "%s" is writable', __DIR__ . '/../dir/path/test'));

        $check2 = new DirWritable('/some/other/dir');
        $check2->setLabel('Check if "/some/other/dir" is writable');

        $this->assertEquals(['fs_dir_path_test' => $check1, 'fs_some_other_dir' => $check2], $this->check->getChecks());
    }
}
