<?php
namespace Model;
// hook model_user_stat_use.php

use App\Model;

class UserStatModel extends Model
{
    // hook model_user_stat_public_start.php
    public $table = 'user_stat';
    //public $index = 'node';
    //public $is_delete = 'is_delete';

    // hook model_user_stat_public_end.php

    // hook model_user_stat_start.php

    // hook model_user_stat_end.php
}

?>