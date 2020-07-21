<?php
namespace Model;
// hook model_super_control_list_use.php

use App\Model;

class SuperControlListModel extends Model
{
    // hook model_super_control_list_public_start.php
    public $table = 'super_control_list';
    public $index = 'id';
    //public $is_delete = 'is_delete';

    // hook model_super_control_list_public_end.php

    // hook model_super_control_list_start.php


    // hook model_super_control_list_end.php
}

?>