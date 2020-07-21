<?php
return [
    'debug' => true,
    'db' => [
        'debug' => true,
        'database_type' => 'mysql',
        'server' => '47.244.216.180',
        'database_name' => 'poker',
        'username' => 'poker',
        'password' => 'jkjjna545anjehb',
         'server' => '127.0.0.1',
        'database_name' => 'poker',
        'username' => 'root',
        'password' => 'root',
        'charset' => 'utf8mb4',
        'port' => 3306,
    ],
//    'db1' => [
//        'database_type' => 'mysql',
//        'database_name' => 'WebDB',
//        'server' => '127.0.0.1',
//        'username' => 'root',
//        'password' => 'root',
//        'charset' => 'utf8mb4',
//        'port' => 3306,
//    ],
    'cache' => [
        'host' => '127.0.0.1',
        'port' => 6379,
        'select' => 0,
        'password' => '',
        'prefix' => 'gd_',
    ],
    'agent_prefit'=>'Z',//代理号前缀
    'config_tabs' => ['main' => '基本设置', 'upload' => '上传设置', 'smtp' => '上传设置', 'rule' => '规则设置'],
    'url_suffix' => 'aspx|ashx|html',
    'app_v' => time(),
    'salt' => 'wZPb~yxvA!ir38&Z',
    'site_name' => 'ZxPHP',
    'session_id' => 'session_id',
    'CashRate' => 3,
    'route_max' => 3
];

?>