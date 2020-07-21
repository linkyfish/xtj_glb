<?php
namespace Model;
// hook model_new_menu_use.php

use App\Model;

class NewMenuModel extends Model
{
    // hook model_new_menu_public_start.php
    public $table = 'new_menu';
    public $index = 'Node';
    //public $is_delete = 'is_delete';

    // hook model_new_menu_public_end.php

    // hook model_new_menu_start.php


    // hook model_new_menu_end.php
}

?>