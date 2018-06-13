<?php

namespace Oro\Bundle\HealthCheckBundle\Check;

use ZendDiagnostics\Check\DirWritable;
use ZendDiagnostics\Check\CheckCollectionInterface;

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

        foreach ($this->checkDirs as $checkDir) {
            $check = new DirWritable($checkDir);
            $check->setLabel(sprintf('Check if "%s" is writable', $checkDir));

            $checks[$this->getCheckShortName($checkDir)] = $check;
        }

        return $checks;
    }

    /**
     * @param string $checkDir
     * @return string
     */
    private function getCheckShortName(string $checkDir): string
    {
        if (strpos($checkDir, $this->rootDir) === 0) {
            $checkDir = ltrim(str_replace($this->rootDir, '', $checkDir), '/');
        }

        $checkDir = preg_replace('/[^[:alpha:]]/', '_', $checkDir);

        return preg_replace('/_{2,}/', '_', sprintf('fs_%s', $checkDir));
    }
}
