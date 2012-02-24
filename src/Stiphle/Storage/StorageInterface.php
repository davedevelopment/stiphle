<?php
/**
 * @package    Stiphle
 * @subpackage Stiphle\Throttle\LeakyBucket\Storage
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
 * Interface describing a persistant storage mechanism for the LeakyBucket
 * throttle
 *
 * @author      Dave Marshall <david.marshall@atstsolutions.co.uk>
 */
interface StorageInterface
{
    /**
     * Set lock wait timout
     *
     * @param int $milliseconds
     */
    public function setLockWaitTimeout($milliseconds);

    /**
     * Lock 
     *
     * We might have multiple requests coming in at once, so we lock the storage
     *
     * @return void
     */
    public function lock($key);

    /**
     * Unlock
     *
     * @return void
     */
    public function unlock($key);

    /**
     * Get 
     *
     * @param string $key
     * @return int
     */
    public function get($key);

    /**
     * set last modified
     *
     * @param string $key
     * @param mixed $value
     * @return void
     */
    public function set($key, $value);
}



