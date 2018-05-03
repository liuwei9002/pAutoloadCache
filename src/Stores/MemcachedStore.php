<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/24
 * Time: 15:25
 */
namespace Suyain\Stores;

use Suyain\Contracts\Store;

class MemcachedStore extends BaseStore implements Store
{
    private static $memcached;

    private static $keyValues = [];

    private static $separator = '!Q@W#E';

    public function __construct($config)
    {
        if (self::$memcached == null) {
            self::$memcached = new \Memcached();
            $servers = [];
            foreach ($config['servers'] as $server) {
                $servers[] = [
                    $server['host'],
                    $server['port'],
                    $server['weight'] ?: 100
                ];
            }
            self::$memcached->addServers($servers);
        }
    }

    public function get($key, $isAutoLoad = true, $waitTime = 0)
    {
        if ($isAutoLoad) {
            return $this->getAutoLoad($key, $waitTime, debug_backtrace());
        }
        return $this->originalGet($key)['realValue'];
    }

    public function put($key, $value, $expir_time = 60)
    {
        $this->originalSet($key, $value, $expir_time);
    }

    public function ttl($key)
    {
        $info = $this->originalGet($key);
        return $info['expirTime'] - (time() - $info['lastLoadTime']);
    }

    public function exist($key)
    {
        return self::$memcached->get($key) ? true : false;
    }

    public function getLock($key)
    {
        return self::$memcached->add($key, 1);
    }

    public function unlock($key)
    {
        return self::$memcached->delete($key);
    }

    public function incr($key)
    {
        self::$memcached->increment($key);
    }

    /**
     * 获取key和加载时间
     *
     * @param $key
     * @return bool|string
     *
     */
    private function originalGet($key)
    {
        if (isset(self::$keyValues[$key])) {
            return self::$keyValues[$key];
        }

        $value = self::$memcached->get($key);
        $separator = explode(self::$separator, $value);
        $lastLoadTime = end($separator);
        $expirTime = prev($separator);

        $realValue = unserialize(substr($value, 0, -1 * strlen(self::$separator . $expirTime . self::$separator . $lastLoadTime)));

        self::$keyValues[$key] = [
            'realValue' => $realValue,
            'lastLoadTime' => $lastLoadTime,
            'expirTime' => $expirTime
        ];

        return self::$keyValues[$key];
    }

    /**
     * 设置时间
     *
     * @param $key
     * @param $value
     * @param $expir_time
     */
    private function originalSet($key, $value, $expir_time)
    {
        $time = time();
        $value = serialize($value) . self::$separator . $expir_time . self::$separator . $time;
        self::$memcached->set($key, $value, $expir_time);
        self::$keyValues[$key] = [
            'realValue' => $value,
            'lastLoadTime' => $time,
            'expirTime' => $expir_time
        ];
    }

}