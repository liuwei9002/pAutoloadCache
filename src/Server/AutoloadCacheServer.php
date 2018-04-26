<?php
namespace Suyain\Server;

use Suyain\Cache\AutoloadHandler;
use Suyain\Cache\CacheLock;
use Suyain\Support\SwooleHandler;

class AutoloadCacheServer {
    // swoole服务
    private static $serv = null;

    // 客户端发送过来的数据
    private static $data = [];

    // 客户端信息
    private static $clientInfo = [];

    public function __construct()
    {
        if (!self::$serv) {
            self::$serv = SwooleHandler::createServer();
        }
    }

    /**
     * 启动服务
     */
    public function run()
    {
        SwooleHandler::startServer(self::$serv, [$this, 'onPacket'], [$this, 'onTask'], [$this, 'onFinish'], 4);
    }

    /**
     * 有数据进来时执行
     *
     * @param $serv
     * @param $data
     * @param $clientInfo
     */
    public function onPacket($serv, $data, $clientInfo)
    {
        $task_id = $serv->task($data);
    }

    /**
     * 有任务时执行
     *
     * @param $serv
     * @param $task_id
     * @param $from_id
     * @param $data {"class":"", "func":"", "args":{},"key":""}
     */
    public function onTask($serv, $task_id, $from_id, $data)
    {
        \logHandler('服务端开始' . microtime(true));
        $data = json_decode($data, true);
        \logHandler('服务端接收到参数' . var_export($data, true));

        // 校验数据有效性
        self::checkParams($data);
        \logHandler('服务端接收到参数校验通过');

        // 关闭自动加载
        AutoloadHandler::closeCache();
        if ('object' == $data['type']) {
            call_user_func_array([$data['class'], $data['func']], isset($data['args']) ? $data['args'] : []);
        } else {
            call_user_func_array($data['class'] . '::' . $data['func'], isset($data['args']) ? $data['args'] : []);
        }

        // 任务完成后执行
        $serv->finish($data['key']);
    }

    /**
     * 任务完成后执行，解锁
     *
     * @param $serv
     * @param $task_id
     * @param $data
     */
    public function onFinish($serv, $task_id, $data)
    {
        CacheLock::unlock($data);
        \logHandler('服务端处理任务' . $task_id . '结束，解锁' . microtime(true));
    }

    /**
     * 校验参数
     *
     * @param $data
     * @throws \Exception
     */
    private function checkParams($data)
    {
        // 数据不能为空
        if (empty($data)) {
            throw new \Exception("data is empty");
        }

        // class参数不能为空
        if (empty($data['class'])) {
            throw new \Exception("class is empty");
        }

        // func 参数不能为空
        if (empty($data['func'])) {
            throw new \Exception("function is empty");
        }

        // 反射判断有效
        $obj = new \ReflectionClass($data['class']);
        // class是否有效
        if (!$obj) {
            throw new \Exception("class is not exist");
        }
        // func是否有效
        if (!$obj->hasMethod($data['func'])) {
            throw new \Exception("function is not exist");
        }
        // 获取方法对象
        $method = $obj->getMethod($data['func']);

        // 不是public方法
        if (!$method->isPublic()) {
            throw new \Exception('the function is note public');
        }

        // 判断参数个数
        if ($method->getNumberOfParameters() != count($data['args'])) {
            throw new \Exception('param number is error');
        }
    }

}