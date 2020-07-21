<?php
namespace Model;
// hook model_lost_control_use.php

use App\Model;

class LostControlModel extends Model
{
    // hook model_lost_control_public_start.php
    public $table = 'lost_control';
    public $index = 'id';
    //public $is_delete = 'is_delete';

    // hook model_lost_control_public_end.php

    // hook model_lost_control_start.php


    // hook model_lost_control_end.php
}

?>