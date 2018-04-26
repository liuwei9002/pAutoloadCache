<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/20
 * Time: 16:31
 */
namespace Suyain\Support;

class Config
{
    private static $globeConfig = [];

    /**
     * 获取配置
     *
     * @param $key
     * @param null $default
     * @return mixed|null
     */
    public static function get($key, $default = null)
    {
        $keys = explode('.', $key);
        $value = self::$globeConfig;
        foreach ($keys as $k) {
            if (!isset($value[$k])) {
                return $default;
            }

            $value = $value[$k];
        }

        return $value ?: $default;
    }

    /**
     * 把值追加到全局配置中
     *
     * @param $key
     * @param $value
     */
    public static function put($key, $value)
    {
        self::$globeConfig[$key] = $value;
    }

}