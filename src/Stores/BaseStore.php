<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/24
 * Time: 16:46
 */

namespace Suyain\Stores;


use Suyain\Cache\AutoloadHandler;

class BaseStore
{
    public function getAutoLoad($key, $waitTime, $backtrace)
    {
        $autoloadHandler = new AutoloadHandler($this, $backtrace);
        return $autoloadHandler->get($key, $waitTime);
    }
}