<?php
/**
 * Author: DRERY <rui1642@foxmail.com>
 */
namespace drery\swoole;
use Qun;

/**
 * Class Server
 * @package swoole\components
 *
 * @property $server_host
 * @property $server_port
 * @property $server_mode
 * @property $server_sock_type
 * @property $protocol
 * @property $swoole_option
 */
class Server{
    public function __construct($config = [])
    {
        if (!empty($config) && is_array($config)) {
            foreach ($config as $key => $value) {
                $this->$key = $value;
            }
        }
        $this->init();
    }

    public function init()
    {
        $host = !empty($this->server_host) ? $this->server_host : '0.0.0.0';
        $port = !empty($this->server_port) ? $this->server_port : 9501;
        $mode = !empty($this->server_mode) ? $this->server_mode : SWOOLE_PROCESS;
        $sock_type = !empty($this->server_sock_type) ? $this->server_sock_type : SWOOLE_SOCK_TCP;
        Qun::$swoole = $server = new \swoole_server($host, $port, $mode, $sock_type);

        $this->swooleInit();
    }

    public function swooleInit()
    {
        $setting['reactor_num'] = !empty($this->reactor_num) ? $this->reactor_num : 4;
        $setting['worker_num'] = !empty($this->worker_num) ? $this->worker_num : 4;
        $setting['max_request'] = !empty($this->max_request) ? $this->max_request : 0;
        $setting['max_conn'] = !empty($this->max_conn) ? $this->max_conn : null;
        $setting['task_worker_num'] = !empty($this->task_worker_num) ? $this->task_worker_num : 4;
        $setting['task_ipc_mode'] = !empty($this->task_ipc_mode) ? $this->task_ipc_mode : 1;
        $setting['task_max_request'] = !empty($this->task_max_request) ? $this->task_max_request : 0;
        $setting['task_tmpdir'] = !empty($this->task_tmpdir) ? $this->task_tmpdir : null;
        $setting['dispatch_mode'] = !empty($this->dispatch_mode) ? $this->dispatch_mode : 2;
        $setting['message_queue_key'] = !empty($this->message_queue_key) ? $this->message_queue_key : null;
        $setting['daemonize'] = !empty($this->daemonize) ? $this->daemonize : 1;
        $setting['backlog'] = !empty($this->backlog) ? $this->backlog : null;
        $setting['log_file'] = !empty($this->log_file) ? $this->log_file : './swoole/runtime/swoole.log';
        $setting['log_level'] = !empty($this->log_level) ? $this->log_level : 5;
        $setting['heartbeat_check_interval'] = !empty($this->heartbeat_check_interval) ? $this->heartbeat_check_interval : 60;
        $setting['heartbeat_idle_time'] = !empty($this->heartbeat_idle_time) ? $this->heartbeat_idle_time : 300;
        $setting['open_eof_check'] = !empty($this->open_eof_check) ? $this->open_eof_check : true;
        $setting['open_eof_split'] = !empty($this->open_eof_split) ? $this->open_eof_split : true;
        $setting['package_eof'] = !empty($this->package_eof) ? $this->package_eof : "\r\n";
        $setting['open_length_check'] = !empty($this->open_length_check) ? $this->open_length_check : null;
        $setting['package_length_type'] = !empty($this->package_length_type) ? $this->package_length_type : null;
        $setting['package_max_length'] = !empty($this->package_max_length) ? $this->package_max_length : null;

        $setting = array_filter($setting, function($val) {
            if ($val === null)
                return false;
            return true;
        });

        Qun::$swoole->set($setting);
    }

    public function run()
    {
        $protocolClass = $this->getProtocolClass();
        /** @var \Drery\swoole\protocols\BaseProtocol $Protocol */
        $Protocol = new $protocolClass($this->swoole_option);
        $Protocol->run();
    }

    public function getProtocolClass()
    {
        if (empty($this->protocol)) {
            switch ($this->server_sock_type) {
                case SWOOLE_SOCK_TCP:
                    $protocol = 'tcp';
                    break;
                case SWOOLE_SOCK_UDP:
                    $protocol = 'udp';
                    break;
                default:
                    $protocol = 'tcp';
            }
        } else {
            $protocol = strtolower($this->protocol);
        }
        return $className = "\\drery\\swoole\\protocols\\".ucfirst($protocol).'Protocol';
    }
}