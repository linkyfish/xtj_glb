<?php
namespace Model;

// hook model_custom_use.php

use App\Model;

class AdminIpWhiteListModel extends Model
{
    // hook model_custom_use.php
    public $table = 'admin_ip_whitelist';
    public $index = 'id';
    //public $is_delete = 'is_delete';
    // hook model_custom_start.php

    public function Add($data){
     return $this->insert($data);
    }

    /**
     * 是否允许的IP
     * @param $adminId
     * @param $ip
     * @return bool
     */
    public function isAllIp($adminId,$ip)
    {
       return $this->count(['adminId'=>$adminId,'ip'=>$ip]) >0 ;
    }
}


?>