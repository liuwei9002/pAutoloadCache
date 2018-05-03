<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/28
 * Time: 17:12
 */

namespace Suyain\Cache;

use Suyain\Support\Config;

class BaseCache
{
    /**
     * 缓存过期时间
     * @var
     */
    public static $expirTime;

    /**
     * 缓存获取不到等待时间
     * @var
     */
    public static $waitTime;

    /**
     * 缓存自动加载开关
     * @var
     */
    public static $openAutoload;

    /**
     * 存放方法参数个数
     */
    private static $_reflectionMethod = [];

    public static function __callStatic($method, $args)
    {
        if (preg_match('/^\w+Cache$/', $method)) {
            return self::callMethodCache($method, $args);
        }
    }

    public static function callMethodCache($method, $params)
    {
        // 获取真实的方法名
        $realMethod = strstr($method, 'Cache',true);
        // 获取真实的参数个数
        $paramsNumber = self::getParamsNumber(static::class, $realMethod);

        // 设置缓存参数
        self::setCacheConfig($paramsNumber, $params);

        // 裁剪超出范围的参数
        $params = array_splice($params, 0, $paramsNumber);

        // 获取缓存的key值
        $cacheKey = self::getParamsCacheKey(...$params);

        $cache = new CacheManager();
        $data = $cache->store()->get($cacheKey, self::$openAutoload, self::$waitTime);
        if (!empty($data)) return $data;

        // 调用不走缓存的方法
        $data = static::$realMethod(...$params);
        $cache->store()->put($cacheKey, $data);
        return $data;
    }

    /**
     * 获取参数cache的key值 md5码
     *
     * @param mixed $params 参数
     * @return string
     */
    public static function getParamsCacheKey(...$params)
    {

        $__index__ = $__class__ = $__method__ = null;
        $data = isset($params[0]) ? $params[0] : null;

        // 获取下标
        $__index__ = isset($data['__index__']) ? $data['__index__'] : null;
        // 获取指定的class名
        $__class__ = isset($data['__class__']) ? $data['__class__'] : null;
        // 获取指定的method名
        $__method__ = isset($data['__method__']) ? $data['__method__'] : null;
        // 判断数据是否存在
        if (!is_null($__index__) || !is_null($__class__) || !is_null($__method__)) {
            unset($params[0]);
            $params = array_values($params);
        } else {
            $__index__ = 2;
        }
        return md5(
        // 调用者的类名
            ($__class__ ?: self::debugBacktrace($__index__, "class", ''))
            // 获取调用此方法的方法名
            .($__method__ ?: self::debugBacktrace($__index__,"function", ''))
            // 获取指定参数
            .self::encode($params)
        );
    }

    /**
     * encode方法
     *
     * @param mixed $params 参数
     * @param bool $second 是否是两层结构的数据
     * @return string
     */
    public static function encode($params, $second = false)
    {
        if (!$second) {
            return serialize($params);
        }

        $return = [];
        array_walk($params, function ($val, $key) use (&$return) {
            $return[$key] = self::encode($val);
        });
        return $return;
    }

    /**
     * 获取调用的详细信息 回溯
     *
     * @param string $string 数组的路径
     * @param mixed $default 默认值
     * @return mixed
     */
    public static function debugBacktrace($index, $string = '', $default = '')
    {
        try {
            $backtrace = debug_backtrace();
            if ($string) {
                return isset($backtrace[$index])
                    ? (isset($backtrace[$index][$string])
                        ? $backtrace[$index][$string]
                        : $default
                    )
                    : $default;
            }
            return $backtrace ?: $default;
        } catch (\Exception $e) {
            return $default;
        }
    }

    /**
     * 获取类方法定义的参数个数
     *
     * @param string $class class名
     * @param string $method method名
     * @return int
     */
    public static function getParamsNumber($class, $method)
    {
        $key = "{$class}::$method";
        if (!isset(self::$_reflectionMethod[$key])) {
            $object = new \ReflectionMethod($class, $method);
            return sizeof($object->getParameters());
        }
        return self::$_reflectionMethod[$key];
    }

    /**
     * 设置缓存配置
     *
     * @param $paramsNumber
     * @param $params
     */
    public static function setCacheConfig($paramsNumber, $params)
    {
        self::$expirTime = Config::get('common.expirTime', 60);
        self::$waitTime = Config::get('common.waitTime', 0.1);
        self::$openAutoload = Config::get('common.openAutoload', true);

        // 设置缓存过期时间
        if (isset($params[$paramsNumber])) {
            self::$expirTime = $params[$paramsNumber];
        }
        // 设置缓存等待时间
        if (isset($params[$paramsNumber + 1])) {
            self::$waitTime = $params[$paramsNumber + 1];
        }

        // 设置是否开启自动加载
        if (isset($params[$paramsNumber + 2])) {
            self::$openAutoload = $paramsNumber[$paramsNumber + 2];
        }
    }

}