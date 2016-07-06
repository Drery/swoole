<?php
/**
 * Author: DRERY <rui1642@foxmail.com>
 */
namespace drery\swoole\listeners;
abstract class BaseListener
{
    abstract public function registerEvent(\swoole_server_port &$port, $option = []);
}