<?php
namespace Model;
// hook model_user_settlement_use.php

use App\Model;

class UserSettlementModel extends Model
{
    // hook model_user_settlement_public_start.php
    public $table = 'user_settlement';
    //public $index = 'node';
    public $is_delete = 'is_delete';

    // hook model_user_settlement_public_end.php

    // hook model_user_settlement_start.php


    // hook model_user_settlement_end.php
}

?>