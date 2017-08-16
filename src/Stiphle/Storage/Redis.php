<?php
namespace Stiphle\Storage;

/**
 * Redis storage via Predis package
 *
 * @author Jacob Christiansen <jacob@colourbox.com>
 */
class Redis implements StorageInterface
{
    protected $lockWaitTimeout = 1000;
    protected $redisClient;

    public function __construct(\Predis\Client $redisClient)
    {
        $this->redisClient = $redisClient;
    }

    /**
     * {@inheritDoc}
     */
    public function setLockWaitTimeout($milliseconds)
    {
        $this->lockWaitTimeout = $milliseconds;
    }

    /**
     * {@inheritDoc}
     */
    public function lock($key)
    {
        $start = microtime(true);

        while (is_null($this->redisClient->set($this->getLockKey($key), 'LOCKED', 'PX', 3600, 'NX'))) {
            $passed = (microtime(true) - $start) * 1000;
            if ($passed > $this->lockWaitTimeout) {
                throw new LockWaitTimeoutException();
            }
            usleep(100);
        }
    }

    /**
     * {@inheritDoc}
     */
    public function unlock($key)
    {
        $this->redisClient->del($this->getLockKey($key));
    }

    /**
     * {@inheritDoc}
     */
    public function get($key)
    {
        return $this->redisClient->get($key);
    }

    /**
     * {@inheritDoc}
     */
    public function set($key, $value)
    {
        $this->redisClient->set($key, $value);
    }

    private function getLockKey($key)
    {
        return $key . "::LOCK";
    }
}
