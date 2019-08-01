<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use ZendDiagnostics\Check\CheckCollectionInterface;
use ZendDiagnostics\Check\DirWritable;

/**
 * Class for check write permissions on folders
 */
class FileStorageCheckCollection implements CheckCollectionInterface
{
    /** @var string */
    protected $rootDir;

    /** @var array */
    protected $checkDirs;

    /**
     * @param string $rootDir
     * @param array $checkDirs
     */
    public function __construct(string $rootDir, array $checkDirs)
    {
        $this->rootDir = $rootDir;
        $this->checkDirs = $checkDirs;
    }

    /**
     * {@inheritdoc}
     */
    public function getChecks(): array
    {
        $checks = [];

        foreach ($this->checkDirs as $key => $checkDir) {
            $check = new DirWritable($checkDir);
            $check->setLabel(sprintf('Check if "%s" is writable', $checkDir));

            $checks[$key] = $check;
        }

        return $checks;
    }
}
