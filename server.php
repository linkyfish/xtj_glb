<?php
//$str = '您尾号5899卡6月16日20:42网上银行收入(微信零钱提现)0.10元，余额12.10元。【工商银行】';
//$str = '您尾号5899卡6月16日21:44快捷致富支出(充值致富宝-致富宝-余额充值)0.10元，余额13.40元。【工商银行】';
//function MsgFmt($msg){
//    $tmp = [
//        '尾号(\d+)卡(\d+)月(\d+)日(\d+):(.+)收入\((.+)\)([\d\.]+)元，余额([\d\.]+)元'=>'gsyh',
//        '尾号(\d+)卡(\d+)月(\d+)日(\d+):(.+)支出\((.+)\)([\d\.]+)元，余额([\d\.]+)元'=>'gsyh'
//    ];
//
//    foreach ($tmp as $k=> $v){
//        preg_match_all('/'.$k.'/isU',$msg,  $arr);
//        if($arr[1]){
//           var_dump($arr);
//            break;
//        }
//    }
//}
//
//MsgFmt($str);
//
//exit;
date_default_timezone_set('PRC');
ini_set('date.timezone', 'Asia/Shanghai');
ini_set('memory_limit', '2048M');


//$arr = range(100000,200000,1);
//file_put_contents('./1.txt',implode(',',$arr));
//echo count($arr),'-';


define('__LV__', 'pro');

define('__GCP__', get_magic_quotes_gpc());
define('IS_CLI', true);
define('IS_DCL', true);
define('DS', DIRECTORY_SEPARATOR);
define('__ROTDIR__', __DIR__ . '/');
define('__CONDIR__', __ROTDIR__ . 'Config/');
define('__SERDIR__', __ROTDIR__ . 'Server/');
define('__COMDIR__', __ROTDIR__ . 'Common/');
define('__VENDIR__', __ROTDIR__ . 'vendor/');
define('__APPDIR__', __ROTDIR__ . 'App/');
define('__TMPDIR__', __ROTDIR__ . 'Tmp/');//  /dev/shm/tmp/
define('__PUBDIR__', __APPDIR__ . 'Public/');
define('__PIDDIR__', __ROTDIR__ . 'Pid/');
define('__WEBDIR__', __ROTDIR__ . 'Web/');
define('__UPFDIR__', __WEBDIR__ . 'uploads');

define('__UPPATH__', '../../uploads/');
define('__CAHDIR__', __ROTDIR__ . 'Cache/');
define('__IDEDIR__', __CAHDIR__ . 'Ide/');//IDE使用
define('__LOGDIR__', __CAHDIR__ . 'Log/');//日志
define('__STADIR__', __CAHDIR__ . 'Stat/');//统计

define('__ISKF__', 0);

$setting = include_once __CONDIR__ . __LV__ . '_setting.php';
include_once __SERDIR__ . 'Server.php';

$server_worker = new \Server\Server('0.0.0.0', $setting);
$server_worker->run();

?>