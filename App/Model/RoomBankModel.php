<?php
namespace Model;

// hook model_room_bank_use.php

use App\Model;

class RoomBankModel extends Model
{
    // hook model_room_bank_use.php
    public $table = 'room_bank';
    public $index = 'roomID';
    // hook model_room_bank_start.php


    // hook model_room_bank_end.php

    /**
     * 获取游戏HTTP接口端口
     * @param $roomId
     * @return string
     */
    public function getGameHttpPortById($roomId)
    {
        if($roomId >= 30004 &&  $roomId  <= 32008  )
        {
            $roomId = $roomId.'1';
        }

        $roomList  = $this->select([],  [],  'moneyGameId,port',1, 1000);
        $port = '9090';
        if($roomId==1 || $roomId==0 )
        {
            return $port;
        }
        foreach ($roomList as  $item)
        {
            if($item['roomID'] == $roomId || $item['moneyGameId'] == $roomId )
            {
                $port = $item['port'];
                break;
            }
        }

        return $port;
    }



    /**
     * 获取游戏服务器细腻
     * @param $roomId
     * @return string
     */
    public function getGameServerInfoById($roomId)
    {
        if($roomId >= 30004 &&  $roomId  <= 32006)
        {
            $roomId = $roomId.'1';
        }
        $ret = $this->read(['roomId'=>$roomId]);
        $arr = [
            'ip'=>_CONF('gameServerIp'),
            'port'=>'9090',
        ];
        if($ret)
        {
            $arr = [
                'ip'=>$ret['server_ip'],
                'port'=>$ret['port'],
            ];
        }
        return $arr ;
    }
}


?>
