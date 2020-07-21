<?php
namespace Model;

// hook model_pay_config_use.php

use App\Model;

class RepayConfigModel extends Model
{
    // hook model_pay_config_use.php
    public $table = 'repay_config';
    public $index = 'ID';

    public $is_delete = 'is_delete';
    // hook model_pay_config_start.php


    // hook model_pay_config_end.php
}


?>