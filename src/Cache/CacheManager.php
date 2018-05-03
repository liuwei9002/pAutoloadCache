<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/20
 * Time: 16:22
 */
namespace Suyain\Cache;

use Suyain\Stores\FactoryStores;
use Suyain\Support\Config;

class CacheManager
{
    protected $stores = [];

    protected $customCreators = [];

    /**
     * 获取驱动名
     *
     * @param null $name
     * @return mixed
     */
    public function store($name = null)
    {
        $name = $name ?: $this->getDefaultDriver();

        if (isset($this->stores[$name])) {
            return $this->stores[$name];
        }

        return $this->stores[$name] = $this->getResolve($name);
    }

    /**
     * 获取操作对象
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    private function getResolve($name)
    {
        if (isset($this->stores[$name])) return $this->stores[$name];

        $config = $this->getStoresConfig($name);

        if (empty($config)) {
            throw new \Exception("Cache store [{$name}] is not defined.");
        }

        if (isset($this->customCreators[$config['driver']])) {
            return $this->customCreators[$config['driver']];
        } else {
            return $this->customCreators[$config['driver']]
                = (new FactoryStores())->createStore($config['driver'], $config);
        }
    }

    /**
     * 获取默认驱动名
     *
     * @return mixed|null
     */
    private function getDefaultDriver()
    {
        return Config::get('cache.default');
    }

    private function getStoresConfig($name)
    {
        return Config::get("cache.stores.{$name}");
    }
}