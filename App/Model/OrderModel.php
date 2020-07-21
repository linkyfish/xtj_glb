<?php
namespace Model;

// hook model_order_use.php

use App\Model;

class OrderModel extends Model
{
    // hook model_order_use.php
    public $table = 'pay_order';
    public $index = 'ID';
    //public $is_delete = 'is_delete';
    // hook model_order_start.php


}


?>