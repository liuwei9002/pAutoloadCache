<?php
/**
 * Created by PhpStorm.
 * User: liuwei
 * Date: 2018/4/25
 * Time: 11:23
 */

namespace Suyain\Support;


class SwooleHandler
{
    /**
     * 创建客户端
     *
     * @return \swoole_client
     * @throws \Exception
     */
    public static function createClientConnect()
    {
        $cli = new \swoole_client(SWOOLE_SOCK_UDP);

        if (!$cli->connect(Config::get('swoole.client_ip'), Config::get('swoole.client_port'), 1)) {
            throw new \Exception('swoole client to create failure');
        }

        return $cli;
    }

    /**
     * 客户端发送信息
     *
     * @param $cli
     * @param $data
     * @throws \Exception
     */
    public static function send($cli, $data)
    {
        if (empty($cli)) {
            throw new \Exception("swoole client to create failure");
        }

        $cli->send($data);
    }

    /**
     * 创建服务
     *
     * @return \swoole_server
     */
    public static function createServer()
    {
        return new \swoole_server(Config::get('swoole.server_ip'), Config::get('swoole.server_port'), SWOOLE_PROCESS, SWOOLE_SOCK_UDP);
    }

    /**
     * 启动server
     *
     * @param $serv
     * @param $onPacket
     * @param $onTask
     * @param $onFinish
     * @param $taskWorkerNum
     */
    public static function startServer($serv, $onPacket, $onTask, $onFinish, $taskWorkerNum)
    {
        $serv->set([
            'task_worker_num' => $taskWorkerNum,
            'task_ipc_mode' => 3, // task进程与worker进程之间通信方式 1：使用unix socket通信 2：消息队列通信 3：消息队列通信，争抢模式
        ]);
        $serv->on('packet', $onPacket);
        $serv->on('task', $onTask);
        $serv->on('finish', $onFinish);
        $serv->start();
    }
}