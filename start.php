<?php
require_once __DIR__ . '/vendor/autoload.php';

$serv = new \Suyain\Server\AutoloadCacheServer();

$serv->run();