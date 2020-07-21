<?php
namespace Model;

// hook model_gm_notice_use.php

use App\Model;

class SmsCodeModel extends Model
{

    // hook model_gm_notice_use.php
    public $table = 'sms_code';
    public $index = 'id';
    //public $is_delete = 'is_delete';
    // hook model_gm_notice_start.php


    public function send($smsChannel,$tpl,$phone,$data){
		$key = 'sms_'.$phone;
    	$r = $this->Usr->CacheGet($key);
		if(!empty($r['time'])){
			return ['code'=>0,'success'=>false,'msg'=>'发送频繁，间隔2分钟'];
		}
		$this->Usr->CacheSet($key,['time'=>time()],120);

        //发送短信
        include_once _include(__APPDIR__ . 'Controller/Api/Interfaces/SmsBase.php');
        if (is_file(__APPDIR__ . 'Controller/Api/Interfaces/' . $smsChannel . '.php')) {
            include_once _include(__APPDIR__ . 'Controller/Api/Interfaces/' . $smsChannel . '.php');
            $contor = '\\Api\\Interfaces\\' . $smsChannel;
            $sms = new $contor($this);
            $ret = $sms->send($data,$phone,$tpl);
            return $ret;
        } else {
            return ['code'=>0,'success'=>false,'msg'=>'短信模块不存在'];
        }

    }
    // hook model_gm_notice_end.php
}


?>