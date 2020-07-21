<?php
namespace Model;
// hook model_sys_notice_use.php

use App\Model;

class SysNoticeModel extends Model
{
    // hook model_sys_notice_public_start.php
    public $table = 'sys_notice';
    public $index = 'MsgID';
    public $is_delete = 'is_delete';

    // hook model_sys_notice_public_end.php

    // hook model_sys_notice_start.php


    // hook model_sys_notice_end.php
}

?>