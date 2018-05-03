<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/24
 * Time: 15:34
 */

namespace Suyain\Stores;


class FactoryStores
{
    /**
     * 创建缓存对象
     *
     * @param $driver
     * @param $config
     * @return mixed
     */
    public function createStore($driver, $config)
    {
        $clazz = 'Suyain\\Stores\\' . ucfirst($driver) . 'Store';
        return new $clazz($config);
    }
}