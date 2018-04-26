<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/24
 * Time: 10:40
 */

namespace Suyain\Cache;

use Suyain\Support\Config;

class CacheLock
{
    protected static $prefix_lock_type = 'lock_type_';

    protected static $lockObj = null;

    /**
     * 获取锁
     *
     * @return bool
     */
    public static function getLock($key)
    {
        return self::LockObj()->getLock(self::getLockKey($key)) ? true : false;
    }

    /**
     * 解锁
     *
     * @return bool
     */
    public static function unlock($key)
    {
        return self::LockObj()->unlock(self::getLockKey($key)) ? true : false;
    }

    /**
     * 获取加锁类型
     *
     * @return mixed|null
     */
    public static function getDefaultLockType()
    {
        return Config::get('common.lock_type_default');
    }

    /**
     * 获取锁对象
     *
     * @return mixed
     */
    public static function LockObj()
    {
        if (self::$lockObj) {
            return self::$lockObj;
        }

        $lock_type = self::getDefaultLockType();

        return self::$lockObj = (new CacheManager())->store($lock_type);
    }

    /**
     * 获取锁的key
     *
     * @param $key
     * @return string
     */
    private static function getLockKey($key)
    {
        return md5(self::$prefix_lock_type . $key);
    }
}