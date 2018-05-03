<?php
namespace Suyain\Demo;

use Suyain\Cache\CacheManager;
use Suyain\Cache\BaseCache;
use Suyain\Support\Config;

class demo extends BaseCache
{
    public static function getList($key)
    {
        usleep(0.05 * 1000000);

        $cache = new CacheManager();
        $cache->store()->incr('use_auto_load');
        return $key . '_value';
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