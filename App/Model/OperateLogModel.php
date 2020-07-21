<?php
namespace Model;
// hook model_operate_log_use.php

use App\Model;

class OperateLogModel extends Model
{
    // hook model_operate_log_public_start.php
    public $table = 'operate_log';
    public $index = 'ID';
    //public $is_delete = 'is_delete';

    // hook model_operate_log_public_end.php

    // hook model_operate_log_start.php

	/**
	 * @title  Add
	 *
	 * @param $AdminID
	 * @param $UsrID
	 * @param $Type 1 Agent 2 Player
	 * @param $IP int
	 * @param $Desc
	 *
	 * @return bool|mixed
	 * 2020/3/1 23:21
	 */
    public function Add($AdminID, $UsrID,$Type, $IP, $Desc)
    {
        $insert = [
            'UserID' => $AdminID,
            'UsrID' => $UsrID,
            'Type' => $Type,
            'CreateAt' => time(),
            'IP' => $IP,
            'Desc' => $Desc,
        ];
        return $this->insert($insert);
    }

    // hook model_operate_log_end.php
}

?>