<?php
namespace Suyain\Contracts;

/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/24
 * Time: 14:11
 */

interface Store
{
    public function get($key, $isAutoLoad, $waitTime);

    public function put($key, $value, $expir_time);

    public function ttl($key);

    public function exist($key);

    public function getLock($key);

    public function unlock($key);
}