<?php
/**
 * @package    Stiphle
 * @subpackage Stiphle\Storage
 */
namespace Stiphle\Storage;

/**
 * This file is part of Stiphle
 *
 * Copyright (c) 2011 Dave Marshall <dave.marshall@atstsolutuions.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Use memcached via PHP's memcached extension
 *
 * @author      Dave Marshall <david.marshall@atstsolutions.co.uk>
 */
class Memcached implements StorageInterface
{
    /**
     * @var int
     */
    protected $lockWaitTimeout = 1000;

    /**
     * @var int  Time to sleep when attempting to get lock in microseconds
     */
    protected $sleep = 100;

    /**
     * @var int 
     */
    protected $ttl = 3600;

    /**
     * Memcached instance
     */
    protected $memcached;

    /**
     * Constructor
     *
     */
    public function __construct(\Memcached $memcached)
    {
        $this->memcached = $memcached;
    }

    /**
     * Set lock wait timeout
     *
     * @param int $milliseconds
     */
    public function setLockWaitTimeout($milliseconds)
    {
        $this->lockWaitTimeout = $milliseconds;
        return;
    }

    /**
     * Set the sleep time in microseconds
     *
     * @param int 
     * @return void
     */
    public function setSleep($microseconds)
    {
        $this->sleep = $microseconds;
        return;
    }

    /**
     * Set the ttl for the apc records in seconds
     *
     * @param int $seconds
     * @return void
     */
    public function setTtl($microseconds)
    {
        $this->ttl = $microseconds;
        return;
    }

    /**
     * Lock 
     *
     * If we're using storage, we might have multiple requests coming in at
     * once, so we lock the storage
     *
     * @return void
     */
    public function lock($key)
    {
        $key = $key . "::LOCK";
        $start = microtime(true);
        
        while(!$this->memcached->add($key, true, $this->ttl)) {
            $passed = (microtime(true) - $start) * 1000;
            if ($passed > $this->lockWaitTimeout) {
                throw new LockWaitTimeoutException();
            }
            usleep($this->sleep);
        }

        return;
    }

    /**
     * Unlock
     *
     * @return void
     */
    public function unlock($key)
    {
        $key = $key . "::LOCK";
        $this->memcached->delete($key);
    }

    /**
     * Get last modified
     *
     * @param string $key
     * @return int
     */
    public function get($key)
    {
        return $this->memcached->get($key);
    }

    /**
     * set 
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->memcached->set($key, $value, $this->ttl);
        return;
    }

}



