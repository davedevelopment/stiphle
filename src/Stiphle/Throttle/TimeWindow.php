<?php

namespace Stiphle\Throttle;

use Stiphle\Storage\LockWaitTimeoutException;
use Stiphle\Storage\StorageInterface;
use Stiphle\Storage\Process;

/**
 * This file is part of Stiphle
 *
 * Copyright (c) 2011 Dave Marshall <dave.marshall@atstsolutuions.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * A throttle based on a fixed time window
 *
 * @author      Dave Marshall <david.marshall@atstsolutions.co.uk>
 */
class TimeWindow implements ThrottleInterface
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     *
     */
    public function __construct()
    {
        $this->storage = new Process();
    }

    /**
     * Throttle
     *
     * @param string $key  - A unique key for what we're throttling
     * @param int $limit   - How many are allowed
     * @param int $milliseconds - In this many milliseconds
     * @return int
     * @throws LockWaitTimeoutException
     */
    public function throttle($key, $limit, $milliseconds)
    {
        /**
         * Try do our waiting without a lock, so may sneak through because of
         * this...
         */
        $wait = $this->getEstimate($key, $limit, $milliseconds);
        if ($wait > 0) {
            usleep($wait * 1000);
        }

        $key = $this->getStorageKey($key, $limit, $milliseconds); 
        $this->storage->lock($key);
        $count = $this->storage->get($key);
        $count++;
        $this->storage->set($key, $count);
        $this->storage->unlock($key);
        return $wait;
    }

    /**
     * Get Estimate (doesn't require lock)
     *
     * How long would I have to wait to make a request?
     *
     * @param string $key  - A unique key for what we're throttling
     * @param int $limit   - How many are allowed
     * @param int $milliseconds - In this many milliseconds
     * @return int - the number of milliseconds before this request should be allowed
     * to pass
     */
    public function getEstimate($key, $limit, $milliseconds)
    {
        $key = $this->getStorageKey($key, $limit, $milliseconds); 
        $count = $this->storage->get($key);
        if ($count < $limit) {
            return 0;
        }

        return $milliseconds - ((microtime(1) * 1000) % (float) $milliseconds);
    }

    /**
     * Get storage key
     *
     * @param string $key  - A unique key for what we're throttling
     * @param int $limit   - How many are allowed
     * @param int $milliseconds - In this many milliseconds
     * @return string
     */
    protected function getStorageKey($key, $limit, $milliseconds)
    {
        $window = $milliseconds * (floor((microtime(1) * 1000)/$milliseconds));
        $date = date('YmdHis', $window/1000);
        return $date . '::' . $key . '::' . $limit . '::' . $milliseconds . '::COUNT'; 
    }

    /**
     * Set Storage
     *
     * @param StorageInterface $storage
     * @return TimeWindow
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

}
