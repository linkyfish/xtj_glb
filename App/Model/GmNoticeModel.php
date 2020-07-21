<?php
namespace Model;

// hook model_gm_notice_use.php

use App\Model;

class GmNoticeModel extends Model
{
    public $notice = [
        '0' => '系统公告',
        '1' => '在线奖励',
        '2' => '分享送',
        '3' => '添加客服送',
        '4' => '注册送'
    ];
    // hook model_gm_notice_use.php
    public $table = 'gm_notice';
    public $index = 'id';
    //public $is_delete = 'is_delete';
    // hook model_gm_notice_start.php


    // hook model_gm_notice_end.php
}


?>