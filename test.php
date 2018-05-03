<?php
require_once __DIR__ . '/vendor/autoload.php';

$demoObj = new Suyain\Demo\demo();
//$ttl = $demoObj->getTtl('auto_load_cache_key_1');
//if (isset($_GET["debug"])) {
//    echo $ttl;die;
//}

//$rand = rand(1, 10);
//$key = "auto_load_cache_key_" . $rand;

$key = "auto_load_cache_key_1";

var_dump( $demoObj::getListCache($key));