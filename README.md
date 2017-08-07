Stiphle
======

Install via Composer
-------

```
composer require davedevelopment/stiphle
```

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
<?php

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

Throttle Strategies
-------------------

There are currently two types of throttles, [Leaky
Bucket](http://en.wikipedia.org/wiki/Leaky_bucket) and a simple fixed time
window.

``` php

/**
 * Throttle to 1000 per *rolling* 24 hours, e.g. the counter will not reset at
 * midnight
 */
$throttle = new Stiphle\Throttle\LeakyBucket;
$throttle->throttle('api.request', 1000, 86400000);

/**
 * Throttle to 1000 per calendar day, counter will reset at midnight
 */
$throttle = new Stiphle\Throttle\TimeWindow;
$throttle->throttle('api.request', 1000, 86400000);

```

__NB:__ The current implementation of the `TimeWindow` throttle will only work on 64-bit architectures!

Storage
-------

Stiphle currently ships with 5 storage engines

* In process
* APC
* Memcached
* Doctrine Cache
* Redis

Stiphle uses the in process storage by default. A different storage engine can
be injected after object creation.

``` php
$throttle = new Stiphle\Throttle\LeakyBucket();
$storage = new \Stiphle\Storage\Memcached(new \Memcached());
$throttle->setStorage($storage);
```

Todo
----

* More Tests!
* Decent *Unit* tests
* More throttling methods
* More storage adapters, the current ones are a little volatile, Mongo,
  Cassandra, MemcacheDB etc

Copyright
---------

Copyright (c) 2011 Dave Marshall. See LICENCE for further details
