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
 * Basic in-process storage of values
 *
 * @author      Dave Marshall <david.marshall@atstsolutions.co.uk>
 */
class Process implements StorageInterface
{
    /**
     * @var int
     */
    protected $lockWaitTimeout = 1000;

    /**
     * @var array
     */
    protected $locked = array();

    /**
     * @var array
     */
    protected $values = array();

    /**
     * Set lock wait timeout
     *
     * @param int $milliseconds
     */
    public function setLockWaitTimeout($milliseconds)
    {
        $this->lockWaitTimeout = $milliseconds;
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
        if (!isset($this->locked[$key])) {
            $this->locked[$key] = false;
        }

        $start = microtime(true);
        while($this->locked[$key]) {
            $passed = (microtime(true) - $start) * 1000;
            if ($passed > $this->lockWaitTimeout) {
                throw new LockWaitTimeoutException();
            }
        }

        $this->locked[$key] = true;

        return;
    }

    /**
     * Unlock
     *
     * @return void
     */
    public function unlock($key)
    {
        $this->locked[$key] = false;
    }

    /**
     * Get 
     *
     * @param string $key
     * @return int
     */
    public function get($key)
    {
        if (isset($this->values[$key])) {
            return $this->values[$key];
        }

        return null;
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
        $this->values[$key] = $value;
    }
}



