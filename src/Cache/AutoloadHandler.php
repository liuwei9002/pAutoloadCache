<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/23
 * Time: 16:19
 */

namespace Suyain\Cache;

use Suyain\Support\SwooleHandler;
use Suyain\Support\Config;

class AutoloadHandler
{
    /**
     * 缓存中是否还存在
     * @var bool
     */
    protected $existKeyFlag = false;

    /**
     * 调用方信息
     *
     * @var array
     */
    protected $callInfo = [];

    /**
     * 缓存渠道
     *
     * @var null
     */
    protected static $driverObj = null;

    /**
     * 调用方调用类别
     *
     * @var array
     */
    protected static $callTypes = [
        '::'  => 'static',
        '->'  => 'object'
    ];

    /**
     * 关闭缓存
     *
     * @var bool
     */
    private static $closeCache = false;

    public function __construct($driverObj, $backtrace)
    {
        $this->setDriverObj($driverObj);

        $this->setBacktrace($backtrace);
    }

    /**
     * 获取缓存
     *
     * @param $key
     * @return mixed
     */
    public function get($key, $waitTime)
    {
        // 如果关闭了自动加载，则解决返回缓存数据
        if (self::$closeCache) {
            \logHandler("关闭缓存");
            return null;
        }

        // 如果需要加载，进行加载
        if ($this->needToLoad($key)) {
            \logHandler('需要加载数据');
            $this->callToLoad($key);
        }

        // 如果缓存存在，则返回缓存数据
        if ($this->existKey($key)) {
            \logHandler('缓存存在，直接返回缓存数据');
            return $this->getCache($key);
        }

        // 等待缓存数据
        return $this->waitCacheData($key, $waitTime);
    }

    /**
     * 等到取数据
     *
     * @param $key
     * @return mixed
     */
    public function waitCacheData($key, $waitTime)
    {
        \logHandler('缓存不存在，等待缓存数据');
        $micr = microtime(true);
        $i = 0;
        while (microtime(true) - $micr <= $waitTime ?: Config::get('common.waitTime')) {
            // 等待一段时间
            usleep(Config::get('common.sleepTime') * 1000000);
//            \logHandler('一次等待缓存数据' . $i);$i++;
            // 判断是否存在key
            if ($this->existKey($key)) {
                \logHandler('等待缓存重新存在');
                return $this->getCache($key);
            }
        }
        \logHandler('等待缓存还是不存在，结束等待，返回null');
        return null;
    }


    /**
     * 是否需要重新加载，缓存快要过期，并且能获取到锁
     *
     * @param $key
     * @return bool
     */
    public function needToLoad($key)
    {
        CacheLock::unlock($key);
        \logHandler('缓存还有' . self::$driverObj->ttl($key) . 's过期');
        if (self::$driverObj->ttl($key) <= Config::get('common.ttl_time')
            && $this->getLock($key)
        ) {
            return true;
        }
        return false;
    }

    /**
     * 获取锁
     *
     * @param $key
     * @return mixed
     */
    private function getLock($key)
    {
        return CacheLock::getLock($key);
    }

    /**
     * 缓存中是否存在key
     * @param $key
     * @return bool
     */
    public function existKey($key)
    {
        if (self::$driverObj->exist($key)) {
            return true;
        }

        return false;
    }

    /**
     * 设置缓存对象
     *
     * @param $obj
     */
    public function setDriverObj($obj)
    {
        self::$driverObj = $obj;
    }

    /**
     * 获取调用者信息
     *
     * @param $backtrace
     */
    public function setBacktrace($backtrace)
    {
        if (empty($backtrace[1])) {
            $this->callInfo = [];
        }

        $this->callInfo = [
            'class' => isset($backtrace[1]['class']) ? $backtrace[1]['class'] : "",
            'func'  => isset($backtrace[1]['class']) ? $backtrace[1]['function'] : "",
            'args'  => isset($backtrace[1]['args']) ? $backtrace[1]['args'] : "",
            'type'  => isset($backtrace[1]['type']) ?
                (isset(self::$callTypes[$backtrace[1]['type']])
                    ? self::$callTypes[$backtrace[1]['type']] : '')
                : ""
        ];
    }

    /**
     * 关闭缓存
     */
    public static function closeCache()
    {
        \logHandler('服务端关闭缓存');
        self::$closeCache = true;
    }

    /**
     * 直接获取缓存数据
     *
     * @param $key
     * @return mixed
     */
    public function getCache($key)
    {
        return self::$driverObj->get($key, false);
    }

    /**
     * 加载数据
     */
    private function callToLoad($key)
    {
        $this->callInfo['key'] = $key;
        $cli = SwooleHandler::createClientConnect();
        SwooleHandler::send($cli, json_encode($this->callInfo));
    }
}