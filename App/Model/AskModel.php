<?php
namespace Model;
// hook model_ask_use.php

use App\Model;

class AskModel extends Model
{

    // hook model_ask_public_start.php
    public $table = 'ask';
    public $index = 'ID';
    public $is_delete = 'IsDelete';
    // hook model_ask_public_end.php

    // hook model_ask_start.php



    // hook model_ask_end.php

}
?>