<?php
/**
 * @package    Stiphle
 * @subpackage 
 */

namespace Stiphle\Throttle;

/**
 * This file is part of Stiphle
 *
 * Copyright (c) 2011 Dave Marshall <dave.marshall@atstsolutuions.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Interface describing a throttle
 *
 * @author      Dave Marshall <david.marshall@atstsolutions.co.uk>
 */
interface ThrottleInterface
{

    /**
     * Throttle
     *
     * @param string $key  - A unique key for what we're throttling
     * @param int $limit   - How many are allowed
     * @param int $milliseconds - In this many milliseconds
     * @return void
     */
    public function throttle($key, $limit, $milliseconds);

    /**
     * Get Estimate
     *
     * If I were to throttle now, how long would I be waiting
     *
     * @param string $key  - A unique key for what we're throttling
     * @param int $limit   - How many are allowed
     * @param int $milliseconds - In this many milliseconds
     * @return int - the number of milliseconds before this request should be allowed
     */
    public function getEstimate($key, $limit, $milliseconds);
}



