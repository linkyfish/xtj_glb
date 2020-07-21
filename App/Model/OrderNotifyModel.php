<?php
namespace Model;

// hook model_order_notify_use.php

use App\Model;

class OrderNotifyModel extends Model
{
    // hook model_order_notify_use.php
    public $table = 'pay_order_notify';
    public $index = 'NotifyID';
    //public $is_delete = 'is_delete';
    // hook model_order_notify_start.php


    // hook model_order_notify_end.php
}


?>