<?php
namespace Model;
// hook model_menu_use.php

use App\Model;

class ImagesModel extends Model
{
    // hook model_menu_public_start.php
    public $table = 'images';
    public $index = 'ID';
    public $is_delete = 'is_delete';

    // hook model_menu_public_end.php

    // hook model_menu_start.php


    // hook model_menu_end.php
}

?>