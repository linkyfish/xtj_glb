<?php
namespace Model;
// hook model_errorcode_use.php

use App\Model;

class AdModel extends Model
{

    // hook model_errorcode_public_start.php
    public $table = 'ad';
    public $index = 'id';

    public $type=[1=>'公告','中奖'];
    // hook model_errorcode_public_end.php

    // hook model_errorcode_start.php



    // hook model_errorcode_end.php

}
?>