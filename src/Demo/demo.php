<?php
namespace Suyain\Demo;

use Suyain\Cache\CacheManager;

class demo
{
    public static function getList($key)
    {
        \logHandler('开始时间'.microtime(true));
        $cache = new CacheManager();
        $data = $cache->store()->get($key, true, 1);

        \logHandler("首次获取缓存数据" . $data);

        if (empty($data)) {
//            sleep(2);
            $data = 'after_' . $key;
            $cache->store()->put($key, $data, 20);
            \logHandler("获取缓存数据为空，再次获取数据" . $data);
        }
        \logHandler('结束时间'.microtime(true));
        var_dump($data);
    }

    public static function addCache($key)
    {
        $cache = new CacheManager();
        $cache->store()->put($key, 'before_' . $key, 2);
    }
}