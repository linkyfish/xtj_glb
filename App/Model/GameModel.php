<?php
namespace Model;

// hook model_chipstrade_use.php

use App\Model;

class GameModel extends Model
{
    // hook model_chipstrade_use.php
    public $table = 'game';
    public $index = 'roomID';
    // hook model_chipstrade_start.php
    public $fengkong;
    public $gamename;

    public $gamenamearray = [];
    public $game_op = [];
    public $byGame = [60001,60002,60003,60004,100002,100003,100004,100005,400002,400003,400004,400005,640001,640002,640003,670001,670002,670003,690001,690002,690003];


    public $lbGame = [];

    public function __construct($server)
    {
        parent::__construct($server);
        $list = $this->RoomBank->select([], [], 'moneyGameId  as ID,remark as Name,fk as FK', 0, 0, '', 'group by ID');
        //$key = ['区域一', '区域二', '区域三', '区域四', '区域五', '区域六', '低级场', '中级场', '高级场', '一 ', '二 ', '三 ', '四 ', '五 ', '六 ', '七 ', '八 ', '九 '];
//        $data = [[]];
//        foreach ($list as &$v){
//            $v['Name'] = rtrim( $v['Name'],'123456789').' ';
//            $v['Name'] = trim(str_replace($key,'',$v['Name']));
//            $data[$v['Name']]['Name']=$v['Name'];
//            $data[$v['Name']]['ID'][]=$v['ID'];
//        }
//
//        $this->game_op = array_values($data);
//        unset($this->game_op[0]);
        $list = array_merge([['ID' => 1, 'Name' => '大厅']],$list);
        $this->game_op = $this->gamenamearray = array_values($list);
        unset( $this->game_op[0]);
        $this->gamename = arrlist_change_key($list, 'ID');
        $this->fengkong = arrlist_values($list, 'ID');
    }


    /**
     * 获取用户在线的游戏名称
     * @param $id
     */
    public function getGameNameByOnlineId($id)
    {


        /* GameState_Offine eGameState = iota //0 不在线
        GameState_Online //1 在线 大厅
        GameState_BJL //2百家乐
        GameState_BJ //3黑杰克
        GameState_Slot //4 拉霸
        GameState_BRNN //5 百人牛牛
        GameState_HHDZ //6 红黑大战
        GameState_LH // 7 龙虎
        GameState_VP //8 翻牌机
        GameState_CF // 9海王
        GameState_CowBoy //10 牛仔
        GameState_GoldFlower //11 扎金花
        GameState_DDZ //12 斗地主
        GameState_FKSG //疯狂水果 13
        GameState_FQZS //飞禽走兽 14
        GameState_JJSK //急速时刻 15
        GameState_HW //海王2 16
        GameState_CRZFISH //疯狂捕鱼 17
        GameState_WXHH //五星宏辉 18
        GameState_BNCF //百鸟朝凤 19
        */

        $map = [
            "0" => "离线",
            "1" => "大厅",
            "2" => "百家乐",
            "3" => "21点",
            "4" => "拉霸机",
            "5" => "百人牛牛",
            "6" => "红黑大战",
            "7" => "龙虎",
            "8" => "翻牌机",
            "9" => "海王",
            "10" => "德州",
            "11" => "扎金花",
            "12" => "斗地主",
            "13" => "疯狂水果",
            "14" => "飞禽走兽",
            "15" => "3D宝马",
            "16" => "海王2",
            "17" => "疯狂捕鱼",
            '18' => '五星宏辉',
            '19' => '捕鸟',
            '20' => '白鸟朝凤',
            '21' => '金蟾捕鱼',

        ];

        $ret = '离线';
        if (isset($map[$id])) {
            $ret = $map[$id];
        }
        return $ret;

    }


    public function getNameById($gameId)
    {
        $game = $this->gamename;
        if (isset($game[$gameId]['Name'])) {
            return $game[$gameId]['Name'];
        }
        $val = '';
        //疯狂捕鱼
        $arrFKBY = [60001, 60002, 60003, 60004, 60005];
        //海王
        $arrHW = [100002, 100003, 100004, 100005];
        //海王2
        $arrHW2 = [400002, 400003, 400004, 400005];
        //海王2
        $arrJC = [690001, 690002, 690003];
        //多人游戏
        $arrDRYX = [200001, 200002, 200003, 200005, 200006];

        //水果机
        $arrSGJ = [30004, 30004, 30004, 30004];

        //croods
        $arrCroods = [30005, 30005, 30005, 30005];

        //金瓶梅
        $arrJPM = [30006, 30006, 30006, 30006];

        //僵尸
        $arrJS = [30007, 30007, 30007, 30007];

        //招财进宝
        $arrZCJB = [30008, 30008, 30008, 30008];

        //舞狮争霸
        $arrWszb = [32005];

        //五龙争霸
        $arrWlzb = [32007];
        //连环夺宝
        $arrLhdb = [51001];

        //大吉大利5
        $arrDJAL = [32001, 32001, 32001, 32001];
        //玉蒲团
        $arrYPT = [32002, 32002, 32002, 32002];
        //财神到
        $arrCSD = [32004, 32004, 32004, 32004];

        //水浒传
        $arrSHZ = [32006, 32006, 32006, 32006];


        //斗地主
        $arrDDZ = [630001, 630004];
        //炸金花
        $arrZJH = [620001, 620004];
        //牛牛
        $arrNN = [70001, 70002, 70003];
        //五星宏辉
        $arrWXHH = [200005];
        //飞禽走兽
        $arrFQZS = [200006];
        //急速时刻
        $arrJSSK = [200002];
        //魔法宝石
        $arrMFBS = [200003];
        //疯狂水果
        $arrFKSG = [200001];
        //红黑-正负盈利
        $arrHH = [80001, 80003, 80002];
        //德州牛仔-正负盈利
        $arrDZNZ = [90001];
        //黑杰克-正负盈利
        $arrHJK = [20001, 20002, 20003];
        //翻牌机--正负盈利
        $arrFPJ = [40001];
        //百家乐正负盈利--正负盈利
        $arrBJL = [10001, 10002, 10003];


        if (in_array($gameId, $arrFKBY, 1))    //疯狂捕鱼
        {
            $val = '疯狂捕鱼';
        } else if (in_array($gameId, $arrHW, 1))      //海王
        {
            $val = '海王';
        } else if (in_array($gameId, $arrHW2, 1))        //海王2
        {
            $val = '海王2';
        }
        else if (in_array($gameId, $arrJC, 1))        //海王2
        {
            $val = '金蟾捕鱼';
        }
        else if (in_array($gameId, $arrSGJ, 1)) //水果机
        {
            $val = '水果机';
        } else if (in_array($gameId, $arrCroods, 1))//croods
        {
            $val = 'croods';
        } else if (in_array($gameId, $arrJPM, 1)) //金瓶梅
        {
            $val = '金瓶梅';
        } else if (in_array($gameId, $arrJS, 1)) //僵尸
        {
            $val = '僵尸';
        } else if (in_array($gameId, $arrZCJB, 1))//招财进宝
        {
            $val = '招财进宝';
        } else if (in_array($gameId, $arrDJAL, 1))//大吉大利
        {
            $val = '大吉大利';
        } else if (in_array($gameId, $arrYPT, 1))//玉蒲团
        {
            $val = '玉蒲团';
        }
        else if (in_array($gameId, $arrCSD, 1))//财神到
        {
            $val = '财神到';
        }
        else if (in_array($gameId, $arrSHZ, 1))//水浒传
        {
            $val = '水浒传';
        }
        else if (in_array($gameId, $arrNN, 1))//财神到
        {
            $val = '百人牛牛';
        } else if (in_array($gameId, $arrBJL, 1))//百家乐
        {
            $val = '百家乐';
        } else if (in_array($gameId, $arrWXHH, 1))//五星宏辉
        {
            $val = '五星宏辉';
        } else if (in_array($gameId, $arrFQZS, 1))//飞禽走兽
        {
            $val = '飞禽走兽';
        } else if (in_array($gameId, $arrJSSK, 1))//急速时刻
        {
            $val = '3D宝马';
        } else if (in_array($gameId, $arrMFBS, 1))//魔法宝石
        {
            $val = '魔法宝石';
        } else if (in_array($gameId, $arrFKSG, 1))//疯狂水果
        {
            $val = '疯狂水果';
        } else if (in_array($gameId, $arrHJK, 1))//黑杰克
        {
            $val = '黑杰克';
        } else if (in_array($gameId, $arrFPJ, 1))//翻牌机
        {
            $val = '翻牌机';
        } else if (in_array($gameId, $arrHH, 1))//红黑
        {
            $val = '红黑';
        } else if (in_array($gameId, $arrDZNZ, 1))//德州牛仔-正负盈利
        {
            $val = '德州牛仔';
        } else if (in_array($gameId, $arrZJH, 1))//炸金花
        {
            $val = '炸金花';
        } else if (in_array($gameId, $arrDDZ, 1))//斗地主
        {
            $val = '斗地主';
        } else if (in_array($gameId, $arrWszb, 1))//舞狮争霸
        {
            $val = '舞狮争霸';

        } else if (in_array($gameId, $arrWlzb, 1))//五龙争霸
        {
            $val = '五龙争霸';
        }
        else if (in_array($gameId, $arrLhdb, 1))//连环夺宝
        {
            $val = '连环夺宝';
        }


        return $val;
    }


    /**
     * 流水ID查询条件
     * @param $gameId
     * @return string
     */
    public function getWhereGameIdIn($gameId)
    {
        $val = [];
        //疯狂捕鱼
//
//
//        if (in_array($gameId, $arrFKBY, 1))    //疯狂捕鱼
//        {
//            $val = $arrFKBY;
//        } else if (in_array($gameId, $arrHW, 1))      //海王
//        {
//            $val = $arrHW;
//        } else if (in_array($gameId, $arrHW2, 1))        //海王2
//        {
//            $val = $arrHW2;
//        } else if (in_array($gameId, $arrSGJ, 1)) //水果机
//        {
//            $val = $arrSGJ;
//        } else if (in_array($gameId, $arrCroods, 1))//croods
//        {
//            $val = $arrCroods;
//        } else if (in_array($gameId, $arrJPM, 1)) //金瓶梅
//        {
//            $val = $arrJPM;
//        } else if (in_array($gameId, $arrJS, 1)) //僵尸
//        {
//            $val = $arrJS;
//        } else if (in_array($gameId, $arrZCJB, 1))//招财进宝
//        {
//            $val = $arrZCJB;
//        } else if (in_array($gameId, $arrDJAL, 1))//大吉大利
//        {
//            $val = $arrDJAL;
//        } else if (in_array($gameId, $arrYPT, 1))//玉蒲团
//        {
//            $val = $arrYPT;
//        } else if (in_array($gameId, $arrCSD, 1))//财神到
//        {
//            $val = $arrCSD;
//        } else if (in_array($gameId, $arrBJL, 1))//百家乐
//        {
//            $val = $arrBJL;
//        } else if (in_array($gameId, $arrWXHH, 1))//五星宏辉
//        {
//            $val = $arrWXHH;
//        } else if (in_array($gameId, $arrFQZS, 1))//飞禽走兽
//        {
//            $val = $arrFQZS;
//        } else if (in_array($gameId, $arrNN, 1))//百人牛牛
//        {
//            $val = $arrNN;
//        } else if (in_array($gameId, $arrJSSK, 1))//急速时刻
//        {
//            $val = $arrJSSK;
//        } else if (in_array($gameId, $arrMFBS, 1))//魔法宝石
//        {
//            $val = $arrMFBS;
//        } else if (in_array($gameId, $arrFKSG, 1))//疯狂水果
//        {
//            $val = $arrFKSG;
//        } else if (in_array($gameId, $arrHJK, 1))//黑杰克
//        {
//            $val = $arrHJK;
//        } else if (in_array($gameId, $arrFPJ, 1))//翻牌机
//        {
//            $val = $arrFPJ;
//        } else if (in_array($gameId, $arrHH, 1))//红黑
//        {
//            $val = $arrHH;
//        } else if (in_array($gameId, $arrDZNZ, 1))//德州牛仔-正负盈利
//        {
//            $val = $arrDZNZ;
//        } else if (in_array($gameId, $arrZJH, 1))//炸金花
//        {
//            $val = $arrZJH;
//        } else if (in_array($gameId, $arrDDZ, 1))//斗地主
//        {
//            $val = $arrDDZ;
//        }

        return $val;
    }


    /**
     * 抽水比例
     * @param $gameId
     * @return bool
     */
    public function getYeildValue($gameId)
    {

        $val = 0;
        //疯狂捕鱼
        $arrYU=[
            60001, 60002, 60003, 60004, 60005,100002, 100003, 100004, 100005,400002, 400003, 400004, 400005,640001,640002,640003,670001,670002,670003
        ];
//        $arrFKBY = [60001, 60002, 60003, 60004, 60005];
//        //海王
//        $arrHW = [100002, 100003, 100004, 100005];
//        //海王2
//        $arrHW2 = [400002, 400003, 400004, 400005];
//        //多人游戏
//        $arrDRYX = [200001, 200002, 200003, 200005, 200006];
//
//        //水果机
//        $arrSGJ = [30004, 30004, 30004, 30004];

//
//        //croods
//        $arrCroods = [30005, 30005, 30005, 30005];
//
//        //金瓶梅
//        $arrJPM = [30006, 30006, 30006, 30006];
//
//        //僵尸
//        $arrJS = [30007, 30007, 30007, 30007];
//
//        //招财进宝
//        $arrZCJB = [30008, 30008, 30008, 30008];
//
//        //大吉大利5
//        $arrDJAL = [32001, 32001, 32001, 32001];
//
//        //玉蒲团
//        $arrYPT = [32002, 32002, 32002, 32002];
//
//        //财神到
//        $arrCSD = [32004, 32004, 32004, 32004];

        //斗地主
        $arrDDZ = [630001, 630004];

        //炸金花
        $arrZJH = [620001, 620002, 620003, 620004];

//        //牛牛
        $arrNN = [70001, 70002, 70003];
//        //红黑-正负盈利计算
//        $arrHH = [80001, 80003, 80002];
//        //德州牛仔-正负盈利
//        $arrDZNZ = [90001];
//        //黑杰克-正负盈利
//        $arrHJK = [20001, 20002, 20003];
//        //翻牌机--正负盈利
//        $arrFPJ = [40001];
//        //正负盈利--正负盈利
        $arrBJL = [10001, 10002, 10003];

        if (in_array($gameId, $arrDDZ))//斗地主
        {
            $val = 5;
        } else if (in_array($gameId, $arrZJH))//炸金花
        {
            $val = 5;
        } else if (in_array($gameId, $arrNN))//牛牛
        {
            $val = -100;
        } else if (in_array($gameId, $arrYU))//捕鱼捕鸟
        {
            $val = -200;
        } else if (in_array($gameId, $arrBJL))//百家乐
        {
            $val = -300;
        } else {
            $val = 0;
        }
        return $val / 100;


    }


    public function forceKickUser($usrid)
    {
        $time = time();
        $formData = [
            'usrid' => $usrid,
            'times' => $time,
            'token' => getApiToken($time),
        ];
        $update_url = 'http://' . _CONF('gameServerIp') . ':9090/' . _CONF('kickPlayerApiUrl');
        $response = post_api($update_url, $formData);
        return $response;
    }

    // hook model_chipstrade_end.php
}


?>