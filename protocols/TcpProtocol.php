<?php
/**
 * Author: DRERY <rui1642@foxmail.com>
 */
namespace drery\swoole\protocols;
use Qun;

class TcpProtocol extends BaseProtocol
{
    public function onConnect()
    {

    }

    public function onReceive()
    {

    }

    public function onClose()
    {

    }

    protected function registerEvent()
    {
        Qun::$swoole->on('connect', [$this, 'onConnect']);
        Qun::$swoole->on('receive', [$this, 'onReceive']);
        Qun::$swoole->on('close', [$this, 'onClose']);
    }
}