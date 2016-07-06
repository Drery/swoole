<?php
/**
 * Author: DRERY <rui1642@foxmail.com>
 */
namespace drery\swoole\protocols;
use Yii;
use Qun;
use yii\web\Application;

abstract class BaseProtocol
{
    public $option;

    public function __construct($option = [])
    {
        $this->option = $option;
    }

    public function onWorkStart(\swoole_server $server, $worker_id)
    {
        require(__DIR__.'/../../../yiisoft/yii2/Yii.php');
        $yiiconfig = require(__DIR__.'/../../../../config/yii.php');
        new Application($yiiconfig);
    }

    public function onTimer()
    {

    }

    public function onTask()
    {

    }

    public function onFinish()
    {

    }

    public function run()
    {
        Qun::$swoole->on('workStart', [$this, 'onWorkStart']);
        $this->registerEvent();
        $this->addListener();
        Qun::$swoole->start();
    }

    abstract protected function registerEvent();

    protected function addListener()
    {
        if (empty($this->option['listener'])) {
            return;
        }
        $listeners = $this->option['listeners'];
        if ($listeners == 'udp') {
            $options = [];
            $className = '\Drery\swoole\listeners\UdpListener';
            /** @var \Drery\swoole\listeners\BaseListener $Listener */
            $Listener = new $className;
            $port_server = Qun::$swoole->addlistener('127.0.0.1', 9502, SWOOLE_SOCK_UDP);
            $Listener->registerEvent($port_server, $options);
        } elseif (is_array($listeners)) {
            foreach ($listeners as $listener) {
                if (empty($listener['protocol']) && empty($listener['class']))
                    continue;
                if (empty($listener['host']) || empty($listener['port']))
                    continue;
                $className = !empty($listener['class']) ? $listener['class'] : "\\drery\\swoole\\listeners\\".ucfirst(strtolower($listener['protocol'])).'Listener';
                $host = $listener['host'];
                $port = $listener['port'];
                $sock_type = !empty($listener['sock_type']) ? $listener['sock_type'] : null;
                unset($listener['class'], $listener['protocol'], $listener['host'], $listener['port'], $listener['sock_type']);
                /** @var \Drery\swoole\listeners\BaseListener $Listener */
                $Listener = new $className;
                $port_server = Qun::$swoole->addlistener($host, $port, $sock_type);
                $Listener->registerEvent($port_server, $listener);
            }
        }
    }
}