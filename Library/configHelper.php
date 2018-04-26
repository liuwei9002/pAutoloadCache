<?php
$globe_base_path = __DIR__ . '/..';
$globe_cache_path = $globe_base_path . "/Cache";
$aaa = require "Config/cache.php";
//var_dump($aaa);die;

\Suyain\Support\Config::put('cache', require('Config/cache.php'));
\Suyain\Support\Config::put('common', require('Config/common.php'));
\Suyain\Support\Config::put('swoole', require('Config/swoole.php'));
