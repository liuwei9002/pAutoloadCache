<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/24
 * Time: 15:25
 */
namespace Suyain\Stores;

use Suyain\Contracts\Store;
use Suyain\Stores\BaseStore;

class RedisStore extends BaseStore implements Store
{
    private $redis;

    public function __construct($config)
    {
        $this->redis = new \Redis();
        $this->redis->connect('127.0.0.1', 6379);
    }

    public function get($key, $isAutoLoad = true, $waitTime = 0)
    {
        if ($isAutoLoad) {
            return $this->getAutoLoad($key, $waitTime, debug_backtrace());
        }
        return $this->redis->get($key);
    }

    public function put($key, $value, $expir_time)
    {
        $this->redis->set($key, $value, $expir_time);
    }

    public function ttl($key)
    {
        return $this->redis->ttl($key);
    }

    public function exist($key)
    {
        return $this->redis->exists($key);
    }

    public function getLock($key)
    {
        return $this->redis->setnx($key, 1);
    }

    public function unlock($key)
    {
        return $this->redis->del($key);
    }
}