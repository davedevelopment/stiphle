<?php

use Stiphle\Storage\LockWaitTimeoutException;
use Stiphle\Storage\StorageInterface;
use Zend\Cache\Storage\StorageInterface as ZendStorageInterface;

class ZendStorage implements StorageInterface
{
    /** @var ZendStorageInterface $cache */
    protected $cache;

    /** @var int */
    protected $lockWaitTimeout;

    /** @var int */
    protected $lockWaitInterval;

    public function __construct(ZendStorageInterface $cache, $lockWaitTimeout = 1000, $lockWaitInterval = 100)
    {
        $this->cache = $cache;
        $this->lockWaitTimeout = $lockWaitTimeout;
        $this->lockWaitInterval = $lockWaitInterval;
    }

    public function setLockWaitTimeout($lockWaitTimeout)
    {
        $this->lockWaitTimeout = $lockWaitTimeout;
    }

    public function lock($key)
    {
        $key = sprintf('%s::LOCK', $key);
        $start = microtime(true);

        while ($this->cache->hasItem($key)) {
            $passed = (microtime(true) - $start) * 1000;
            if ($passed > $this->lockWaitTimeout) {
                throw new LockWaitTimeoutException();
            }

            usleep($this->lockWaitInterval);
        }

        $this->cache->setItem($key, true);
    }

    public function unlock($key)
    {
        $key = sprintf('%s::LOCK', $key);
        $this->cache->removeItem($key);
    }

    public function get($key)
    {
        return $this->cache->getItem($key);
    }

    public function set($key, $value)
    {
        $this->cache->setItem($key, $value);
    }
}
