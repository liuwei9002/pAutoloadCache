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
    private static $redis;

    public function __construct($config)
    {
        if (self::$redis == null) {
            if (isset($config['cluster']) && $config['cluster']) { // 集群
                $this->createClusterObject($config['connections']);
            } else {
                $this->createSimpleObejct(current($config['connections']));
            }
        }
    }

    public function get($key, $isAutoLoad = true, $waitTime = 0)
    {
        if ($isAutoLoad) {
            return $this->getAutoLoad($key, $waitTime, debug_backtrace());
        }
        return self::$redis->get($key);
    }

    public function put($key, $value, $expir_time = 60)
    {
        self::$redis->set($key, $value, $expir_time);
    }

    public function ttl($key)
    {
        return self::$redis->ttl($key);
    }

    public function exist($key)
    {
        return self::$redis->exists($key);
    }

    public function getLock($key)
    {
        return self::$redis->setnx($key, 1);
    }

    public function unlock($key)
    {
        return self::$redis->del($key);
    }

    public function incr($key)
    {
        self::$redis->incr($key);
    }

    private function createClusterObject($connects)
    {
         $connectHost = [];
         foreach ($connects as $connect) {
             $connectHost[] = $connect['host'] . ":" . $connect['port'];
         }
        self::$redis = new \RedisCluster(null, $connectHost);
    }

    private function createSimpleObejct($connect)
    {
        self::$redis = new \Redis();
        self::$redis->connect($connect['host'], $connect['port']);
    }

}