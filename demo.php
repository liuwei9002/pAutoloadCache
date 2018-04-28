<?php
require_once __DIR__ . '/vendor/autoload.php';

$demoObj = new Suyain\Demo\demo();

$demoObj->addCache("test_cache");

for($i=1; $i<=10;$i++){
    $key = "auto_load_cache_key_" . $i ;
    $demoObj->add($key);
}



// (new Suyain\Demo\demo)->getList('test_cache');

//$cli = new swoole_client(SWOOLE_SOCK_UDP);
//$cli->connect('127.0.0.1', '6667', 1);
//
//$i = 0;
//while ($i < 100) {
//    $cli->send("aaa" . $i);
//    $i++;
//}

