<?php
namespace Model;

// hook model_user_lock_log_use.php

use App\Model;

class UserLockLogModel extends Model
{
    // hook model_user_lock_log_public_start.php
    public $table = 'user_lock_log';


    // hook model_user_lock_log_public_end.php


    // hook model_user_lock_log_start.php

    /**
     * Add
     * @auth true
     * @login true
     * @menu false
     * @param $UserID
     * @param $AdminID
     * @param $IP
     * @param int $Type 1 lock 2 unlock
     * @return bool|mixed
     */
    public function Add($UserID, $AdminID, $IP, $Type = 1)
    {
        $insert = [
            'UserID' => $UserID,
            'AdminID' => $AdminID,
            'IP' => $IP,
            'Type' => $Type,
            'CreateAT'=>time()
        ];
        return $this->insert($insert);
    }
    // hook model_user_lock_log_end.php
}

?>