<?php

namespace Server;


if (extension_loaded("swoole")) {
    if (version_compare(SWOOLE_VERSION, '4.4.4', '>=') == false) {
        exit("swoole扩展版本必须大于等于4.4.3\n");
    }
} else {
    exit("必须安装swoole扩展\n");
}

if (version_compare(PHP_VERSION, '7.0.0', '>=') == false) {
    exit("PHP版本必须大于等于7.0.0\n");
}

/**
 * Class Server
 * @package Server
 * @property \Request $request
 * @property \swoole_websocket_server $http_server
 * @property App $app
 */
class Server
{
    const VERSION = '1.1.9';
    public $name = "ZxPHP";
    private $_startFile = '';
    private $pidFile = '';
    private $logFile = '';
    private $set = array('daemonize' => false);
    public $http_server;
    private $app;

    public function __construct($ip = '0.0.0.0', $set = array())
    {
        $this->error();
        $this->init();
        $this->parseCommand();
        $protocol = '\\' . $set['protocol'];
        $this->http_server = new $protocol($ip, $set['port']);
        $this->set['log_file'] = $this->logFile;
        if (empty($set)) {
            $set["enable_reuse_port"] = true;
        }
        $this->set = array_merge($this->set, $set);
        $this->http_server->set($this->set);
        $this->http_server->on('start', [$this, 'onMasterStart']);
        $this->http_server->on('managerstop', [$this, 'onManagerStop']);
        $this->http_server->on('managerstart', [$this, 'onManagerStart']);
        $this->http_server->on('workerstart', [$this, 'onWorkerStart']);
        $this->http_server->on('workererror', [$this, 'onWorkerError']);
        $this->http_server->on('workerexit', [$this, 'onWorkerExit']);
        $this->http_server->on('request', [$this, 'onRequest']);
//        $this->http_server->on('connect', [$this, 'onConnect']);
        $this->http_server->on('message', [$this, 'onMessage']);
        $this->http_server->on('task', [$this, 'onTask']);
        $this->http_server->on('finish', [$this, 'onFinish']);
        $this->http_server->on('close', [$this, 'onClose']);
        $this->http_server->on('open', [$this, 'onOpen']);
        $this->http_server->on('pipemessage', [$this, 'onPipeMessage']);
        define('__VERSION__', self::VERSION);
        define('__WNUM__', $set['worker_num']);
        define('__TNUM__', $set['task_worker_num']);

        $this->Table();
        define('PLUGIN_OFFICIAL_URL', 'http://plugin.zxphp.top/');
    }


//	public function onConnect(\swoole_server $server,$fd,$reactorId){
//		var_dump($server->getClientInfo($fd));
//	}

    public function onRequest($request, $response)
    {
        (new App($this))->run($request, $response);
    }

    public function onTask($server, $task)
    {
        if (is_file(__COMDIR__ . 'onTask.php')) {
            include _include(__COMDIR__ . 'onTask.php');
        } else {
            $ret = (new App($this))->task($task->data);
            $task->finish($ret ? $task->data['controller'] . '_' . $task->data['action'] . ' ' . $ret : '');
        }
    }

    public function onFinish($serv, $task_id, $response)
    {
        !empty($response) && xn_log(xn_json_encode($response), 'task_finish');
    }


    public function onMessage($server, $frame)
    {
        $_data = xn_json_decode($frame->data);
        if (null != $_data) {
			(new App($this))->talk($frame->fd,$_data);
        }
    }


    public function onClose($server, $fd)
    {
		(new App($this))->close($fd);

    }


    public function onOpen($server, $request)
    {
		(new App($this))->open($server,$request);
    }

    public function Table()
    {

        $workers = new \swoole_table(204800);
        $workers->column('lock', \swoole_table::TYPE_INT);
        $workers->create();
        $this->http_server->tmp_lock = $workers;

        $workers = new \swoole_table(102400);
        $workers->column('ms', \swoole_table::TYPE_INT);
        $workers->column('count', \swoole_table::TYPE_INT);
        $workers->create();
        $this->http_server->request_stat = $workers;
        //echo bcdiv($workers->memorySize, 1024 * 1024, 4), 'M ';

        $workers = new \swoole_table(__WNUM__ + __TNUM__ + 2);
        $workers->column('request', \swoole_table::TYPE_INT);
        $workers->column('last_request', \swoole_table::TYPE_INT);
        $workers->column('memory', \swoole_table::TYPE_INT);
        $workers->create();
        $this->http_server->workers_stat = $workers;

        $workers = new \swoole_table(102400);
        $workers->column('views', \swoole_table::TYPE_INT);
        $workers->create();
        $this->http_server->views = $workers;
        //echo bcdiv($workers->memorySize, 1024 * 1024, 4), 'M ';
        $workers = new \swoole_table(409600);
        $workers->column('uid', \swoole_table::TYPE_INT);
        $workers->create();
        $this->http_server->fd_uid = $workers;
        $workers = new \swoole_table(409600);
        $workers->column('num', \swoole_table::TYPE_INT);
        $workers->column('last', \swoole_table::TYPE_INT);
        $workers->create();
        $this->http_server->ip_lock = $workers;
        //echo bcdiv($workers->memorySize, 1024 * 1024, 4), 'M ';

        $tables = include __CONDIR__ . 'Tables.php';
        foreach ($tables as $k => $table) {
            $workers = new \swoole_table($table['num']);
            foreach ($table['fields'] as $_k => $v) {
                if (!empty($v[1])) {
                    $workers->column($_k, $v[0], $v[1]);
                } else {
                    $workers->column($_k, $v[0]);
                }
            }
            $workers->create();
            $this->http_server->$k = $workers;
            //echo bcdiv($workers->memorySize, 1024 * 1024, 4), 'M ';
        }

    }

    protected function error()
    {
        error_reporting(E_ERROR);
        set_error_handler(function ($code, $message, $file, $line) {
            $message = sprintf("\nM：%s \nF：%s \nL：%s\n", $message, $file, $line);
            self::log('ERROR1 ' . $message);
            throw new \Exception($message, '500');
        }, E_ERROR);

        set_exception_handler(function (\Throwable $error) {
            echo $message = sprintf("\nM：%s \nF：%s \nL：%s\n", $error->getMessage(), $error->getFile(), $error->getLine());
            self::log('ERROR2 ' . $message);
            throw new \Exception($message, '500');
            //return false;
        });

        register_shutdown_function(function () {
            $error = error_get_last();
            if (empty($error['type'])) {
                return false;
            }
            $message = '';
            switch ($error['type']) {
                case E_WARNING:
                case E_NOTICE:
                    return true;
                case E_ERROR:
                    $message = ' E_ERROR ';
                    break;
                case E_PARSE:
                    $message = ' E_PARSE ';
                    break;
                case E_CORE_ERROR:
                    $message = ' E_CORE_ERROR ';
                    break;
                case E_COMPILE_ERROR:
                    $message = ' E_COMPILE_ERROR ';
                    break;
                default:
            }
            $message .= sprintf("\nM：%s \nF：%s \nL：%s\n", $error['message'], $error['file'], $error['line']);
            self::log('ERROR3 ' . $message);
            throw new \Exception($error['message'], '500');
        });
    }



    protected function init()
    {
        !defined('__LV__') AND define('__LV__', 'dev');
        !defined('__GCP__') AND define('__GCP__', get_magic_quotes_gpc());
        !defined('IS_CLI') AND define('IS_CLI', true);
        !defined('DS') AND define('DS', DIRECTORY_SEPARATOR);
        !defined('__ROTDIR__') AND define('__ROTDIR__', dirname(__DIR__) . '/');
        !defined('__CONDIR__') AND define('__CONDIR__', __ROTDIR__ . 'Config/');
        !defined('__SERDIR__') AND define('__SERDIR__', __ROTDIR__ . 'Server/');
        !defined('__COMDIR__') AND define('__COMDIR__', __ROTDIR__ . 'Common/');

        !defined('__VENDIR__') AND define('__VENDIR__', __ROTDIR__ . 'vendor/');
        !defined('__APPDIR__') AND define('__APPDIR__', __ROTDIR__ . 'App/');
        !defined('__PLUDIR__') AND define('__PLUDIR__', __APPDIR__ . 'Plugin/');
        !defined('__PIDDIR__') AND define('__PIDDIR__', __ROTDIR__ . 'Pid/');
        !defined('__WEBDIR__') AND define('__WEBDIR__', __ROTDIR__ . 'Web/');
        !defined('__UPFDIR__') AND define('__UPFDIR__', __WEBDIR__ . 'uploads/');
        !defined('__PUBDIR__') AND define('__PUBDIR__', __APPDIR__ . 'Public/');
        !defined('__UPPATH__') AND define('__UPPATH__', '../../uploads/');
        !defined('__CAHDIR__') AND define('__CAHDIR__', __ROTDIR__ . 'Cache/');
        !defined('__IDEDIR__') AND define('__IDEDIR__', __CAHDIR__ . 'Ide/');
        !defined('__LOGDIR__') AND define('__LOGDIR__', __CAHDIR__ . 'Log/');
        !defined('__STADIR__') AND define('__STADIR__', __CAHDIR__ . 'Stat/');//统计
        !defined('__RELOAD__') AND define('__RELOAD__', 0);//开发环境 毫秒重载时间 >0 执行 30000
        !defined('__TMPDIR__') AND define('__TMPDIR__', __ROTDIR__ . 'Tmp/');//Linux下可以直接丢进 内存 /dev/shm
        !is_dir(__PIDDIR__) AND mkdir(__PIDDIR__, 0777, 1) && chmod(__PIDDIR__, 0777);
        !is_dir(__STADIR__) AND mkdir(__STADIR__, 0777, 1) && chmod(__STADIR__, 0777);
        !is_dir(__UPFDIR__) AND mkdir(__UPFDIR__, 0777, 1) && chmod(__UPFDIR__, 0777);
        !is_dir(__TMPDIR__) AND mkdir(__TMPDIR__, 0777, 1) && chmod(__TMPDIR__, 0777);
        !is_dir(__IDEDIR__) AND mkdir(__IDEDIR__, 0777, 1) && chmod(__IDEDIR__, 0777);

        $backtrace = debug_backtrace();
        $this->_startFile = $backtrace[0]['file'];
        $this->pidFile = __PIDDIR__ . str_replace('/', '_', $this->_startFile) . ".pid";
        $this->logFile = __CAHDIR__ . 'Server/Server_' . date('Ymd') . '.log';
        !is_dir(__CAHDIR__ . 'Server/') AND mkdir(__CAHDIR__ . 'Server/', 0777, 1) && chmod(__CAHDIR__ . 'Server/', 0777);
        //加载框架文件
        $libs = glob(__SERDIR__ . 'Libs/*.php');
        foreach ($libs as $_file) {
            require_once $_file;
        }
        $Func = glob(__SERDIR__ . 'Func/*.php');
        foreach ($Func as $_file) {
            require_once $_file;
        }
        $Class = glob(__SERDIR__ . 'Class/*.php');
        foreach ($Class as $_file) {
            require_once $_file;
        }

        require_once __VENDIR__ . 'autoload.php';
    }

    protected function parseCommand()
    {
        global $argv;
        $start_file = $argv[0];
        if (!isset($argv[1])) {
            exit("Usage: php yourfile.php {start|stop|restart|reload}\n");
        }
        $command1 = trim($argv[1]);
        $command2 = isset($argv[2]) ? $argv[2] : '';
        $mode = '';
        if ($command1 === 'start') {
            if ($command2 === '-d') {
                $mode = 'in DAEMON mode';
            } else {
                $mode = 'in DEBUG mode';
            }
        }


        // Get master process PID.
        $master_pid = is_file($this->pidFile) ? file_get_contents($this->pidFile) : 0;
        $master_is_alive = $master_pid && posix_kill($master_pid, 0) && posix_getpid() != $master_pid;

        if ($master_is_alive) {
            if ($command1 === 'start') {
                $this->log("Server [$start_file] already running");
                exit;
            }
        } elseif ($command1 !== 'start' && $command1 !== 'restart') {
            $this->log("Server [$start_file] not run");
            exit;
        }

        switch ($command1) {
            case 'start':
                rmdir_tmp();
                if ($command2 === '-d') {
                    $this->set['daemonize'] = true;
                }
                $this->log("Server [$start_file] $command1 $mode");
                break;
            case 'restart':
            case 'stop':
                $this->log("Server [$start_file] is stoping ...");
                $master_pid && posix_kill($master_pid, SIGTERM);
                $timeout = 5;
                $start_time = time();
                // Check master process is still alive?
                while (1) {
                    $master_is_alive = $master_pid && posix_kill($master_pid, 0);
                    if ($master_is_alive) {
                        // Timeout?
                        if (time() - $start_time >= $timeout) {
                            $this->log("Server [$start_file] stop fail");
                            exit;
                        }
                        // Waiting amoment.
                        usleep(10000);
                        continue;
                    }
                    // Stop success.
                    $this->log("Server [$start_file] stop success");
                    if ($command1 === 'stop') {
                        exit(0);
                    }
                    rmdir_tmp();
                    $this->set['daemonize'] = true;
                    break;
                }
                break;
            case 'reload':
                rmdir_tmp();
                if ($command2 === '-g') {
                    $sig = SIGQUIT;
                } else {
                    $sig = SIGUSR1;
                }
                posix_kill($master_pid, $sig);
                self::log("Server [$start_file] reload success");
                exit;
            default :
                exit("Usage: php yourfile.php {start|stop|restart|reload}\n");
        }
    }

    public function onMasterStart($serv)
    {
        if (false === @file_put_contents($this->pidFile, $serv->master_pid)) {
            throw new \Exception('can not save pid to ' . $this->pidFile);
        }
        PHP_OS != 'Darwin' && swoole_set_process_name("Server: master process " . $this->name . " start_file=" . $this->_startFile);
        //仅供支持IDE从模型获取
        $include_model_files = glob(__APPDIR__ . 'Model/*Model.php');
        foreach ($include_model_files as $model_files) {
            $name = str_replace([__APPDIR__ . 'Model/', 'Model.php'], '', $model_files);
            $str[] = " * @property \Model\\{$name}Model \$$name";
        }

        //从开启的插件中获取model
        $model_name = plugin_get_model();
        foreach ($model_name as $name) {
            $str[] = " * @property \Model\\{$name}Model \${$name}";
        }


        $str = implode("\n", $str);
        IDE_include(__ROTDIR__ . 'Server/Libs/Controller.php', $str);
        IDE_include(__ROTDIR__ . 'Server/Libs/Model.php', $str);
        //以上为IDE提供支持
        plugin_clear_tmp_dir();


    }

    public function onManagerStop($serv)
    {

        if (is_file(__COMDIR__ . 'onManagerStop.php')) {
            include _include(__COMDIR__ . 'onManagerStop.php');
        } else {
            unlink($this->pidFile);
        }
    }


    public function onManagerStart($serv)
    {
        if (is_file(__COMDIR__ . 'onManagerStart.php')) {
            include _include(__COMDIR__ . 'onManagerStart.php');
        } else {
            PHP_OS != 'Darwin' && swoole_set_process_name("Server: manage process " . $this->name . " start_file=" . $this->_startFile);
        }
    }

    public function onWorkerStart(\swoole_server $serv)
    {
        if (is_file(__COMDIR__ . 'onWorkerStart.php')) {
            include _include(__COMDIR__ . 'onWorkerStart.php');
        } else {
            PHP_OS != 'Darwin' && swoole_set_process_name("Server: worker process " . $this->name . " start_file=" . $this->_startFile);
            //echo 'Worker ' . $serv->worker_id . ($serv->taskworker == true ? ' Task ' : ' Worker ') . "\n";
            if (function_exists('opcache_reset')) {
                opcache_reset();
            }
            if (function_exists('apc_clear_cache')) {
                apc_clear_cache();
            }

            $_ENV['conf'] = include __CONDIR__ . __LV__ . '_config.php';
            $_ENV['err_code'] = include __CONDIR__ .  'Error.php';
            $_ENV['conf']['url_suffix'] = explode('|', $_ENV['conf']['url_suffix']);
            $_ENV['cache_class'] = $_ENV['conf']['cache'] ? new \Cache($_ENV['conf']['cache']) : NULL;

            if (!empty($_ENV['conf']['db'])) {
                $_ENV['db_class']['db'] = new \Db($_ENV['conf']['db']);
            }


            for ($i = 1; $i < 5; $i++) {
                if (!empty($_ENV['conf']['db' . $i])) {
                    $_ENV['db_class']['db' . $i] = new \Db($_ENV['conf']['db' . $i]);
                }
            }


            $_ENV['plugin_srcfiles'] = [];
            $_ENV['plugin_paths'] = [];
            $_ENV['plugins'] = [];// 跟官方插件合并
            $_ENV['official_plugins'] = [];// 官方插件列表
            $_ENV['g_include_slot_kv'] = [];// 官方插件列表

            plugin_init();//插件初始化
            plugin_get_hook();//获取启用的钩子
            plugin_get_model();
            plugin_get_controller();
            require_once _include(__COMDIR__ . 'Common.php');

            ConfigReload();
            IncludeFiles();

            require_once _include(__COMDIR__ . 'Tick.php');

        }
    }

    public function onWorkerError($serv, $worker_id, $worker_pid, $exit_code)
    {
        $this->log("worker_pid: " . $worker_pid . " exit_code:" . $exit_code . ' ' . $serv->getLastError());
        $last = $serv->workers_stat->get($worker_id);
        $d = $serv->stats();
        $istask = $serv->taskworker ? 'M' : 'W';
        $workerIdk = $worker_id . $istask;
        if ($last != false) {
            empty($d['worker_request_count']) AND $d['worker_request_count'] = 0;
            $serv->workers_stat->set($workerIdk, array('request' => $last['request'] + ($d['worker_request_count'] - $last['last_request']), 'last_request' => 0, 'memory' => memory_get_usage()));
        } else {
            $serv->workers_stat->set($workerIdk, array('request' => 0, 'last_request' => 0, 'memory' => memory_get_usage()));
        }
    }



    public function onWorkerExit($server, $worker_id)
    {
        //echo 'WorkerExit ' . $worker_id . "\r\n";
    }



    public function onPipeMessage($serv, $src_worker_id, $data)
    {

    }

    public function log($msg)
    {
        xn_log($msg, 'php_error');
        if (empty($this->set['daemonize'])) {
            echo date('Y-m-d H:i:s') . " " . $msg . "\n";
        }
    }

    public function run()
    {
//        include __SERDIR__.'SocketIOParser.php';
//        $socketioHandler = SocketIOParser::getInstance();
//
//        $socketioHandler->on('connection', function ($socket) {
//            echo 'connection:'.$socket->id.PHP_EOL;
//        });
//        $socketioHandler->on('disconnect', function ($socket) {
//            echo 'disconnected:'.$socket->id.PHP_EOL;
//        });
//        $socketioHandler->on('message', function ($socket, $data) {
//            echo 'message:'.PHP_EOL; print_r($data);
//            $socket->emit('message', ['hello' => 'message received']);
//            $socket->disconnect();
//        });
//        $socketioHandler->on('message_with_callback', function ($socket, $data, $ack = '') {
//            echo 'message_with_callback:'.PHP_EOL; print_r($data);
//            $ack && $ack('hello there');
//        });

//        $socketioHandler->bindEngine($this->http_server);

        $this->http_server->start();
    }
}

?>