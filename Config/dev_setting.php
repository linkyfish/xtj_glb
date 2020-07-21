<?php
return [
    'protocol' => 'swoole_websocket_server',
    'worker_num' => 2,
    'task_worker_num' => 1,
    'max_coroutine' => 30000,
    'log_level' => 1,
    'port' => 8364,
    //'user' => 'www',
    'enable_coroutine' => true,
    'task_enable_coroutine'=>true,
//	'hook_flags' => SWOOLE_HOOK_ALL | SWOOLE_HOOK_CURL,
//        'tcp_fastopen' => true,
//        'reload_async' => true,
    'enable_static_handler' => true,
    'upload_tmp_dir' => __UPFDIR__,
    'document_root' => __WEBDIR__,
    'buffer_output_size' => 32 * 1024 *1024, //必须为数字
    'package_max_length'=>1024*1024*5,//最大5m文件
];

?>