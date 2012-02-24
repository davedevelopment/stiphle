Stiphle
======

What is it?
-----------

Stiphle is a little library to try and provide an easy way of throttling/rate limit requests, for those without fancy hardware etc.

How does it work?
-----------------

You create a throttle, and ask it how long you should wait. For example, given
that $identifier is some means of identifying whatever it is you're throttling,
and you want to throttle it to 5 requests per second:

``` php
<?php

$throttle = new Stiphle\Throttle\LeakyBucket;
$identifier = 'dave';
while(true) {
    // the throttle method returns the amount of milliseconds it slept for
    echo $throttle->throttle($identifier, 5, 1000);
}
# 0 0 0 0 0 200 200....

```

Use combinations of values to provide bursting etc, though use carefully as it
screws with your mind

``` php 
$throttle = new Stiphle\Throttle\LeakyBucket;
$identifier = 'dave';
for(;;) {
    /**
     * Allow upto 5 per second, but limit to 20 a minute - I think
     **/
    echo "a:" . $throttle->throttle($identifier, 5, 1000);
    echo " b:" . $throttle->throttle($identifier, 20, 60000);
    echo "\n";
}
#a:0 b:0
#a:0 b:0
#a:0 b:0
#a:0 b:0
#a:0 b:0
#a:199 b:0
#a:200 b:0
#a:199 b:0
#a:200 b:0
#a:200 b:0
#a:199 b:0
#a:200 b:0
#a:199 b:0
#a:200 b:0
#a:200 b:0
#a:199 b:0
#a:200 b:0
#a:200 b:0
#a:199 b:0
#a:200 b:0
#a:199 b:0
#a:200 b:2600
#a:0 b:3000
#a:0 b:2999


```

Todo
----

* More Tests!
* Decent *Unit* tests
* More throttling methods
* More storage adapters, the current ones are a little volatile, Redis, Mongo,
  Cassandra, MemcacheDB etc

Copyright
---------

Copyright (c) 2011 Dave Marshall. See LICENCE for further details
