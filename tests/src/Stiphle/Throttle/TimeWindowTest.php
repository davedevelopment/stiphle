<?php

namespace Stiphle\Throttle;

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
class TimeWindowTest extends PHPUnit_Framework_TestCase
{
    /** @var TimeWindow */
    protected $throttle;

    public function setup()
    {
        $this->throttle = new TimeWindow();
    }

    /**
     * Really crap test here, without mocking the system time, it's difficult to
     * know when you're going to throttled...
     */
    public function testGetEstimate()
    {
        $timeout = strtotime('+5 seconds', microtime(1));
        $count = 0;
        while (microtime(1) < $timeout) {
            $wait = $this->throttle->throttle('dave', 5, 1000);
            if (microtime(1) < $timeout) {
                $count++;
            }
        }

        $this->assertEquals(25, $count);
    }
}


