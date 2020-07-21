<?php
return [
    'protocol' => 'swoole_websocket_server',
    'worker_num' => 8,
    'task_worker_num' => 4,
    'max_coroutine' => 30000,
    'log_level' => 1,
    'port' => 50012,
    //'user' => 'www',
    'enable_coroutine' => true,
    'task_enable_coroutine'=>true,
//        'tcp_fastopen' => true,
//        'reload_async' => true,
    'enable_static_handler' => true,
    'upload_tmp_dir' => __UPFDIR__,
    'document_root' => __WEBDIR__,
    'buffer_output_size' => 32 * 1024 *1024, //必须为数字
    'package_max_length'=>1024*1024*5,//最大5m文件
];

?>