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
        if (strpos($checkDir, $this->projectDir) === 0) {
            $checkDir = ltrim(str_replace($this->projectDir, '', $checkDir), '/');
        }

        $checkDir = preg_replace('/[^[:alpha:]]/', '_', $checkDir);

        return preg_replace('/_{2,}/', '_', sprintf('fs_%s', $checkDir));
    }
}
