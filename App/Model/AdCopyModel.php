<?php
namespace Model;
// hook model_errorcode_use.php

use App\Model;

class AdCopyModel extends Model
{

    // hook model_errorcode_public_start.php
    public $table = 'ad_copy';
    public $index = 'id';
    public $type=[1=>'公告',2=>'中奖'];
}
?>