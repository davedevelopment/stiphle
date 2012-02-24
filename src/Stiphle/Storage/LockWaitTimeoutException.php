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
 * Thrown when a request for a lock timesout
 *
 * @author      Dave Marshall <david.marshall@atstsolutions.co.uk>
 */
class LockWaitTimeoutException extends \Exception {}
