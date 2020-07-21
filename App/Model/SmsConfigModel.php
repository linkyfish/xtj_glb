<?php
namespace Model;

// hook model_pay_config_use.php

use App\Model;

class SmsConfigModel extends Model
{
    // hook model_pay_config_use.php
    public $table = 'sms_config';
    public $index = 'ID';
    public $Channel = [
        10 => '验证码',
        20 => '营销短信',
    ];
}


?>