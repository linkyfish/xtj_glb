<?php
namespace Model;

// hook model_pay_config_use.php

use App\Model;

class PayConfigModel extends Model
{
    // hook model_pay_config_use.php
    public $table = 'pay_config';
    public $index = 'ID';

    public $Channel=[
      10=>'支付宝',
      20=>'微信',
      30=>'银联',
      40=>'信用卡',
      50=>'云闪付',
    ];

    public $is_delete = 'is_delete';
    // hook model_pay_config_start.php


    // hook model_pay_config_end.php
}


?>