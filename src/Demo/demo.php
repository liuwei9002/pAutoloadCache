<?php
namespace Suyain\Demo;

use Suyain\Cache\CacheManager;

class demo
{
    public static function getList($key)
    {
        \logHandler('开始时间'.microtime(true));
        $cache = new CacheManager();
        $data = $cache->store()->get($key, false, 0.1);

        \logHandler("首次获取缓存数据" . $data);

        if (empty($data)) {
            usleep(0.1 * 1000000);
            $cache->store()->incr('use_auto_load');
            $data = $key . '_value';
            $cache->store()->put($key, $data, 60);
            \logHandler("获取缓存数据为空，再次获取数据" . $data);
        }
        \logHandler('结束时间'.microtime(true));
        var_dump($data);
    }

    public static function addCache($key)
    {
        $cache = new CacheManager();
        echo $cache->store()->get('use_auto_load', false);
        $cache->store()->put('use_auto_load', 0, 600);
    }

    public static function add($key){
        $cache = new CacheManager();
        $cache->store()->put($key, $key . '_value', 60);
    }

    public static function getTtl($key)
    {
        $cache = new CacheManager();
        return $cache->store()->ttl($key);
    }
}