<?php

namespace Stiphle\Storage;

use Doctrine\Common\Cache\Cache;

/**
 * This file is part of Stiphle
 *
 * Copyright (c) 2011 Dave Marshall <dave.marshall@atstsolutuions.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

class DoctrineCache implements StorageInterface
{
    protected $lockWaitTimeout = 1000;
    protected $lockWaitInterval = 100;

    public function __construct(Cache $cache, $lockWaitTimeout = 1000, $lockWaitInterval = 100)
    {
        $this->cache = $cache;
        $this->lockWaitTimeout = $lockWaitTimeout;
        $this->lockWaitInterval = $lockWaitInterval;
    }

    public function setLockWaitTimeout($milliseconds)
    {
        $this->lockWaitTimeout = $milliseconds;
        return;
    }

    public function setSleep($microseconds)
    {
        $this->sleep = $microseconds;
        return;
    }

    public function lock($key)
    {
        $key = $key . "::LOCK";
        $start = microtime(true);
        while ($this->cache->contains($key)) {
            $passed = (microtime(true) - $start) * 1000;
            if ($passed > $this->lockWaitTimeout) {
                throw new LockWaitTimeoutException();
            }
            usleep($this->sleep);
        }
        $this->cache->save($key, true);

        return;
    }

    public function unlock($key)
    {
        $key = $key . "::LOCK";
        $this->cache->delete($key);
    }

    public function get($key)
    {
        return $this->cache->fetch($key);
    }

    public function set($key, $value)
    {
        $this->cache->save($key, $value);
        return;
    }

}



