<?php
/**
 * @package
 * @subpackage
 */
namespace Stiphle\Storage;

use \PHPUnit_Framework_TestCase;

/**
 * This file is part of Stiphle
 *
 * Copyright (c) 2011 Dave Marshall <dave.marshall@atstsolutuions.co.uk>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * TITLE
 *
 * DESCRIPTION
 *
 * @author      Dave Marshall <david.marshall@atstsolutions.co.uk>
 */
class ApcTest extends PHPUnit_Framework_TestCase
{
    protected $storage = null;

    public function setup()
    {
        $this->storage = new Apc();
    }

    public function tearDown()
    {
        apc_delete('dave::LOCK');
    }

    /**
     * @expectedException Stiphle\Storage\LockWaitTimeoutException
     */
    public function testLockThrowsLockWaitTimeoutException()
    {
        if (!ini_get('apc.enable_cli') && !ini_get('apcu.enable_cli')) {
            $this->markTestSkipped('APC and APCu needs enabling for the cli via apc.enable_cli=1 or apcu.enable_cli=1');
        }

        $this->storage->lock('dave');        
        $this->storage->lock('dave');
    }


    public function testLockRespectsLockWaitTimeoutValue()
    {
        if (!ini_get('apc.enable_cli') && !ini_get('apcu.enable_cli')) {
            $this->markTestSkipped('APC and APCu needs enabling for the cli via apc.enable_cli=1 or apcu.enable_cli=1');
        }

        /**
         * Test we can do this 
         */
        $this->storage->lock('dave');
        try {
            $start = microtime(1);
            $this->storage->lock('dave');
        } catch (LockWaitTimeoutException $e) {
            $caught = microtime(1);
            $diff   = $caught - $start;
            if (round($diff) != 1) {
                $this->markTestSkipped("Don't think the timings will be accurate enough, expected exception after 1 second, was $diff");
            }
        }

        $this->storage->setLockWaitTimeout(2000);
        try {
            $start = microtime(1);
            $this->storage->lock('dave');
            $this->fail("should not get to this point");
        } catch (LockWaitTimeoutException $e) {
            $caught = microtime(1);
            $diff   = $caught - $start;
            $this->assertEquals(2, round($diff), "Exception thrown after approximately 2000 milliseconds");
        }       
    }
}


