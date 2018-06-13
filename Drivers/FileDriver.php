<?php

namespace Oro\Bundle\HealthCheckBundle\Drivers;

use Lexik\Bundle\MaintenanceBundle\Drivers\DriverTtlInterface;
use Lexik\Bundle\MaintenanceBundle\Drivers\FileDriver as BaseFileDriver;

/**
 * File driver for Maintenance Mode check
 */
class FileDriver extends BaseFileDriver implements DriverTtlInterface
{
    /**
     * Write ttl to lock file
     *
     * @return bool|resource
     */
    protected function createLock()
    {
        $handle = fopen($this->filePath, 'w+');
        if (isset($this->options['ttl']) && (int)$this->options['ttl']) {
            fwrite($handle, time() + $this->options['ttl']);
        }
        return $handle;
    }

    /**
     * Return true if file exists even ttl was expired so maintenance mode must still be on
     *
     * @return bool
     */
    public function isExists(): bool
    {
        return file_exists($this->filePath);
    }

    /**
     * Check if maintenance has ttl and if it is expired
     *
     * @return bool
     */
    public function isExpired()
    {
        if (!$this->hasTtl()) {
            return false;
        }
        $now = new \DateTime('now');
        $accessTime = date('Y-m-d H:i:s', filemtime($this->filePath));
        $accessTime = new \DateTime($accessTime);
        $accessTime->modify(sprintf('+%s seconds', $this->getTtl()));

        return ($accessTime < $now);
    }

    /**
     * {@inheritdoc}
     */
    public function setTtl($value)
    {
        $this->options['ttl'] = $value;
        //in case if file already exists update it with the ttl
        if (file_exists($this->filePath)) {
            $handle = fopen($this->filePath, 'w+');
            fwrite($handle, time() + $this->options['ttl']);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTtl()
    {
        return file_exists($this->filePath)
            ? file_get_contents($this->filePath) - filemtime($this->filePath)
            : $this->options['ttl'];
    }

    /**
     * {@inheritdoc}
     */
    public function hasTtl()
    {
        return file_exists($this->filePath) ?: isset($this->options['ttl']);
    }
}
