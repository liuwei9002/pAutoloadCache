<?php
require_once __DIR__ . '/vendor/autoload.php';

$serv = new \Suyain\Server\AutoloadCacheServer();

$serv->run();

//$serv = new swoole_server('127.0.0.1', '6667', SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
//
//$serv->set(['task_worker_num' => 4]);
//$serv->on('packet', function($serv, $data, $clientInfo){
//    $serv->task($data);
//});
//
//$serv->on('task', function($serv, $task_id, $from_id, $data){
//    usleep(0.03 * 1000000);
//    file_put_contents('/tmp/lw/lw.log',
//        sprintf('[%s][%s]%s%s',posix_getpid(), microtime(true), var_export($data, true), PHP_EOL),
//        FILE_APPEND);
//    $serv->finish($data);
//});
//
//$serv->on('finish', function($serv, $task_id, $data){
//    echo $task_id . '---' . $data;
//});
//
//$serv->start();