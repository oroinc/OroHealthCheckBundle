<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use Laminas\Diagnostics\Check\CheckCollectionInterface;
use Laminas\Diagnostics\Check\DirWritable;

/**
 * Class for check write permissions on folders
 */
class FileStorageCheckCollection implements CheckCollectionInterface
{
    /** @var string */
    protected $projectDir;

    /** @var array */
    protected $checkDirs;

    /**
     * @param string $projectDir
     * @param array $checkDirs
     */
    public function __construct(string $projectDir, array $checkDirs)
    {
        $this->projectDir = $projectDir;
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
