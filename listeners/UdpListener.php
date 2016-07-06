<?php
/**
 * Author: DRERY <rui1642@foxmail.com>
 */
namespace drery\swoole\listeners;

class UdpListener extends BaseListener
{
    public function registerEvent(\swoole_server_port &$port, $option = [])
    {
        $port->set($option);
        $port->on('packet', [$this, 'onPacket']);
    }

    public function onPacket(\swoole_server $server, $data, $client_info)
    {

    }
}