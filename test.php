<?php
require_once __DIR__ . '/vendor/autoload.php';

$demoObj = new Suyain\Demo\demo();
$ttl = $demoObj->getTtl('auto_load_cache_key_1');
if (isset($_GET["debug"])) {
    echo $ttl;die;
}

//$rand = rand(1, 10);

$key = "auto_load_cache_key_1";

echo $demoObj::getListCache($key);