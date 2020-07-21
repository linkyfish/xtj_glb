<?php
namespace Model;


// hook model_moneytrans_use.php

class MoneyTransAgentModel extends \App\Model
{
    // hook model_moneytrans_public_start.php
    public $table = 'moneytrans';
    public $index = 'id';

    public $Type = [
        1 => '免费领取',
        2 => '绑定账号',
        3 => '任务领取',
        4 => '连续登陆',
        5 => '救济金',
        6 => '升级奖励',
        7 => '银行取钱',
        8 => '敲金猪',
        9 => '转盘奖励',
        10 => '七天转盘奖励',
        11 => '在线奖励',
        17 => '商城上分赠送',
        25 => '注册送',
        29 => '申请送',
        35 => '系统送',
        36 => '活动送',
        37 => '申请提现',
        38 => '取消提现',
        39 => '禁用提现',
        50 => '短信送',
        51 => '手工上分',
        52 => '手工减分',
        53 => '手工减银行分',
        61 => '商城上分',
        62 => '领取奖励',
        71 => '代理结算',
        114 => '活动清零',
    ];
    public $need_sub_chips = [400002, 400003, 400004, 400005, 640001, 640002, 640003, 670001, 670002, 670003, 60001, 60002, 60003, 60004, 60005,690001, 690002, 690003];

    // hook model_moneytrans_public_end.php

    // hook model_moneytrans_start.php


    public function ChipsDate($sData, $eData, $cond = [])
    {
        $cond = ['times' => ['>=' => $sData . ' 00:00:00', '<=' => $eData . ' 59:59:59'], 'style' => [51, 61]];
        $list = $this->select($cond, [], 'times,chips,usrid');
        $data = [];
        foreach ($list as $v) {
            list($regtime,) = explode(' ', $v['times']);
            $v['regdate'] = $regtime;
            $Total[$regtime][$v['usrid']] = 1;
        }
        foreach ($Total as $k => $v) {
            $data['TotalNum'][$k] = count($v);
        }
        return $data;
    }

    public function Add($usrid, $chips, $style, $BeginBlance, $action_id, $action_ip)
    {
        $insert = [
            'usrid' => $usrid,
            'chips' => $chips,
            'diamond' => 0,
            'transtype' => $this->Type[$style],
            'style' => $style,
            'BeginBlance' => $BeginBlance,
            'EndBlance' => $BeginBlance + $chips,
            'action_id' => $action_id,
            'times' => date('Y-m-d H:i:s'),
            'action_time' => time(),
            'action_ip' => $action_ip,
        ];
        return $this->insert($insert);
    }

    public function Sub($usrid, $chips, $style, $BeginBlance, $action_id, $action_ip)
    {
        $insert = [
            'usrid' => $usrid,
            'chips' => $chips,
            'diamond' => 0,
            'transtype' => $this->Type[$style],
            'style' => $style,
            'BeginBlance' => $BeginBlance,
            'EndBlance' => $BeginBlance + $chips,
            'action_id' => $action_id,
            'times' => date('Y-m-d H:i:s'),
            'action_time' => time(),
            'action_ip' => $action_ip,
        ];
        return $this->insert($insert);
    }


    public function PlayerMaxWin($cond, $player, $order = -1)
    {

        $list = $this->select($cond, [], 'usrid,style,diamond,BeginBlance,EndBlance,gameID');
        $groups = arrlist_group($list, 'usrid');
        $array = [];
        foreach ($groups as $usrid => $group) {//把游戏记录按照玩家分组
            $row = $this->getZhangDataItem($group);
            $row['usrid'] = $usrid;
            $array[] = $row;
        }
        $data = [];
        $agent_id = arrlist_values($player, 'agent_id');
        $agent = arrlist_key_values($this->User->select(['id' => $agent_id], [], 'id,username'), 'id', 'username');
        $users = arrlist_change_key($player, 'usrid');
        //$r3= [];
        foreach ($array as $v) {
            if(empty($v['usrid'])){
                continue;
            }
            $agent_id = $users[$v['usrid']]['agent_id'];
            $data[] = [
                "ScoreNum" => calculate($users[$v['usrid']]['total_uppoints'] - $users[$v['usrid']]['total_cash']),
                "Agent" => isset($agent[$agent_id]) ? $agent[$agent_id] : '-',
                "TotalWin" => calculate($v['Game_Win']),
                "Account" => $users[$v['usrid']]['openid'],
                "idx" => $v['usrid'],
                "MoneyNum" => calculate($users[$v['usrid']]['chips'] + $users[$v['usrid']]['bankchips']),
                "TotalBet" => calculate($v['Game_Bet']),
            ];
        }
        $data1 = arrlist_multisort($data, 'TotalWin', false);
        $data2 = arrlist_multisort($data, 'TotalWin', true);
        $r['r1'] = array_slice($data1, 0, 50);
        $r['r2'] = array_slice($data2, 0, 50);
        //$r['r3'] = $r3;
        return $r;
    }

    public function GameList($cond, $page, $size, $data = [])
    {

        if (empty($data)) {
            $list = $this->select($cond, [], 'id,usrid,chips,diamond,times,style,transtype,gameID,tableID,BeginBlance,EndBlance,gameNumb');
        } else {
            $list = $data;
        }
        $list = arrlist_sort_by_many_field($list, 'times', SORT_DESC, 'id', SORT_DESC);
        $data = ['results' => []];
        $game = [];
        $gameKey = [];
        $i = -1;
        foreach ($list as $k => $v) {
            if ($v['style'] == 39) {
                continue;
            }
            $vv["GameName"] = $this->Game->getNameById($v['gameID']);
            $vv["tableID"] = $v["tableID"];
            $vv["WalletMoney"] = 0;
            $vv["style"] = $v['style'];
            $vv["UserID"] = $v['usrid'];
            if (in_array($v['gameID'], $this->need_sub_chips)) {
                $vv["TotalWin"] = $v['chips'] > 0 ? calculate($v['chips']) : 0;
            } else if ($v['diamond'] == 0) {
                $vv["TotalWin"] = calculate($v['chips']);
            } else if (-$v['chips'] != $v['diamond']) {
                $vv["TotalWin"] = calculate($v['diamond'] + $v['chips']);
            } else {
                $vv["TotalWin"] = "0.00";
            }
            $v['style'] == 6 && $vv['chips'] = $v['diamond'] + $v['chips'];

            if ($v['gameID'] != 0 && ($v['gameID'] != 1000002 || ($v['gameID'] == 1000002 && $v['diamond'] == 0))) {
                $yeildVal = $this->Game->getYeildValue($v['gameID']);
                if ($yeildVal == -1) {
                    $vv['TotalBet'] = calculate($v['diamond']);
                    $vv["WalletMoney"] = 0;
                    $vv["TotalWin"] = calculate($v['chips']);
                } else if ($yeildVal > 0) {
                    $vv['TotalBet'] = calculate($v['diamond']);
                    $vv["WalletMoney"] = calculate($v['diamond'] * $yeildVal);
                } else {
                    $vv['TotalBet'] = calculate($v['diamond']);
                    $vv["WalletMoney"] = 0;
                }
                if ($v['gameNumb'] == 0) {
                    $i++;
                    //$t = date('_YmdHi', strtotime($v['times']));
                } else {
                    $t = $v['gameNumb'];
                    if ($v['gameID'] == 32006 || $v['gameID'] == 30008 || $v['gameID'] == 30005) { //水浒传
                        if($v['transtype']=='小玛丽'){
                            $t.= mt_rand(10000,100000);
                            $vv['TotalBet'] = '小玛丽';
                        }elseif($v['transtype'] == '30线老虎机比倍成功'|| $v['transtype'] == '30线老虎机比倍失败'){
                            $t.= $v['id'];
                            $vv['tableID']='比倍';
                        }elseif( $v['transtype'] == '30线老虎机猜星成功'|| $v['transtype'] == '30线老虎机猜星扣钱'){
                            $t.= $v['id'];
                            $vv['tableID']='猜星';
                        }
                    }
                    if (isset($game[$v['gameID']][$t])) {
                        if ($v['gameID'] == 40001 || $v['gameID'] == 30008) //翻牌机招财进宝特殊处理
                        {
                            //if( strtotime($gameKey[$v['gameID']][$t]['times'])-strtotime($v['times'])>10) //翻牌机延迟翻牌处理
                            if (strtotime($gameKey[$v['gameID']][$t]['times']) - strtotime($v['times']) > 10) //翻牌机延迟翻牌处理
                            {
                                $i++;
                                $data['results'][$i]['TotalBet'] = calculate($v['chips']);
                                $data['results'][$i]['TotalWin'] = calculate($v['chips']);
                                $data['results'][$i]['BeginBlance'] = calculate($v['BeginBlance']);
                                $data['results'][$i]['EndBlance'] = calculate($v['EndBlance']);
                            } else {
                                $ii = $game[$v['gameID']][$t];
                                if ($yeildVal == -1) {
                                    $data['results'][$ii]['TotalBet'] = $vv['TotalBet'];
                                } else {
                                    $data['results'][$ii]['TotalBet'] = bcadd($data['results'][$ii]['TotalBet'], $vv['TotalBet'], 2);
                                }
                                $data['results'][$ii]['TotalWin'] = bcadd($data['results'][$ii]['TotalWin'], $vv['TotalWin'], 2);
                                $data['results'][$ii]['BeginBlance'] = calculate($v['BeginBlance']);
                                continue;
                            }

                        } else {
                            $ii = $game[$v['gameID']][$t];
                            if ($yeildVal == -1) {
                                $data['results'][$ii]['TotalBet'] = $vv['TotalBet'];
                            } else {
                                $data['results'][$ii]['TotalBet'] = bcadd($data['results'][$ii]['TotalBet'], $vv['TotalBet'], 2);
                            }
                            $data['results'][$ii]['TotalWin'] = bcadd($data['results'][$ii]['TotalWin'], $vv['TotalWin'], 2);
                            $data['results'][$ii]['BeginBlance'] = calculate($v['BeginBlance']);
                            continue;
                        }



                    } else {
                        $i++;
                        $game[$v['gameID']][$t] = $i;
                        $gameKey[$v['gameID']][$t] = $v;
                        if ($v['gameID'] == 40001) {
                            $data['results'][$i]['TotalBet'] = calculate($v['chips']);
                            $data['results'][$i]['TotalWin'] = calculate($v['chips']);
                            $data['results'][$i]['BeginBlance'] = calculate($v['BeginBlance']);
                            $data['results'][$i]['EndBlance'] = calculate($v['EndBlance']);
                        }
                    }
                }
                if ($v['gameNumb'] > 0) {
                    $v['diamond'] == 0 && $vv['TotalBet'] = '免费游戏';
                    if ($v['gameID'] == 40001) {
                        $v['diamond'] == 0 && $vv['TotalBet'] = '延迟翻牌';
//					} elseif ($v['transtype'] == '百家乐开和返钱') {
//						$vv['tableID'] = '开合';
//						$vv['TotalBet'] = calculate( $v['chips']);
//						$vv['TotalWin'] = calculate($v['chips']+$v['chips']);
                    } elseif ($v['gameID'] == 32006 || $v['gameID'] == 30008 || $v['gameID'] == 30005|| $v['gameID'] == 30007) { //水浒传
                        if($v['transtype']=='小玛丽'){
                            $vv['TotalBet'] = '小玛丽';
                        }elseif($v['transtype'] == '30线老虎机比倍成功' || $v['transtype'] == '30线老虎机比倍失败'){
                            $vv['tableID']='比倍';
                        }elseif($v['transtype'] == '30线老虎机猜星成功'||$v['transtype'] == '猜星本金'){
                            $vv['tableID']='猜星';
                            $chips =$v['chips']/2;
                            $vv['TotalBet'] = calculate( $chips);
                            if($v['transtype'] == '猜星本金')
                            {
                                $vv['TotalBet'] = calculate($v['diamond']);
                                $vv['tableID']='猜星扣本金';
                            }
                            //	$vv['TotalWin'] = calculate($v['chips']+$chips);
                            //$vv['TotalBet'] =calculate( $v['chips']/2);
                        }
                        elseif( $v['gameID'] == 30008)
                        {
                            if( $v['diamond']==0)
                            {
                                // $v['diamond'] == 0 && $vv['TotalBet'] = '猜星失败';
                                $v['diamond'] == 0 && $vv['TotalBet'] = '免费游戏';
                            }
                        }
                        elseif( $v['gameID'] == 30005 || $v['gameID'] == 30007 )
                        {
                            if($v['transtype'] == 'Bonus特殊游戏')
                            {
                                $vv['tableID']='特殊游戏';
                            }
                        }

                    }
                } else if ($yeildVal == -2 && $v['diamond'] == 0) {
                    $vv['TotalBet'] = str_replace('玩家', '', $v['transtype']) != $v['transtype'] ? '特殊鱼' : $v['transtype'];
                }
            } else {
                $i++;
                $vv['TotalBet'] = calculate($v['diamond']);
            }

            if ($v['gameID'] == 51001 ) //连环夺宝龙珠探宝特殊处理
            {
                if($v['transtype'] == '龙珠探宝下注'|| $v['transtype'] == '龙珠探宝赢取'){

                    $vv['tableID']='龙珠探宝';
                }
            }

            $vv["BeginBlance"] = calculate($v["BeginBlance"]);
            $vv["EndBlance"] = calculate($v["EndBlance"]);

            $vv["PlayTime"] = $v["times"];
            $vv["GameID"] = $v['gameID'];
            //$vv["transtype"] = $v['transtype'];
            $vv["RecordIndex"] = $v['gameNumb'];

            empty($vv['GameName']) && $vv["GameName"] = $v['transtype'];
            $data['results'][$i] = $vv;
        }
        $data['total'] = $i + 1;
        $data['results'] = array_slice($data['results'], ($page - 1) * $size, $size);
        foreach ($data['results'] as &$v) {
            $yeildVal = $this->Game->getYeildValue($v['GameID']);
            if ($yeildVal == -1) {
                $v['TotalWin'] = bcadd($v['TotalWin'], $v['TotalBet'], 2);
            }
        }
        return $data;
    }

    public function GameList2($cond, $page, $size)
    {
        $_usrid = $cond['usrid'];
        unset($cond['usrid']);
        $list = $this->select($cond, [], 'id,usrid,chips,diamond,times,style,transtype,gameID,tableID,BeginBlance,EndBlance,gameNumb');

        $list = arrlist_sort_by_many_field($list, 'times', SORT_DESC, 'id', SORT_DESC);

        $data['results'] = [];
        $usrids = arrlist_values($list, 'usrid');
        $users = $usrids ? arrlist_key_values($this->Usr->select(['usrid' => $usrids], [], 'usrid,openid'), 'usrid', 'openid') : $usrids;
        $game = [];
        $i = -1;

        foreach ($list as $k => $v) {
            if (empty($v['gameNumb'])) {
                continue;
            }
            $usrid = $v['usrid'];
            $vv["GameID"] = $v['gameID'];
            $vv["RecordIndex"] = $v['gameNumb'];
            $vv["UserID"] = $v['usrid'];

            if (in_array($v['gameID'], $this->need_sub_chips)) {
                $vv["TotalWin"] = $v['chips'] > 0 ? calculate($v['chips'] - $v['diamond']) : 0;
            } else if ($v['diamond'] == 0) {
                $vv["TotalWin"] = calculate($v['chips']);
            } else if (-$v['chips'] != $v['diamond']) {
                $vv["TotalWin"] = calculate($v['diamond'] + $v['chips']);
            } else {
                $vv["TotalWin"] = "0.00";
            }
            $vv["TotalWin"] < 0 && $v['diamond'] = -$vv["TotalWin"] && $vv["TotalWin"] = 0;
            $vv["WalletMoney"] = 0;
            $yeildVal = $this->Game->getYeildValue($v['gameID']);
            if ($yeildVal == -1) {
                $vv['TotalBet'] = calculate($v['diamond']);
                $vv["WalletMoney"] = 0;
                $vv["TotalWin"] = calculate($v['chips']);
            } else if ($yeildVal > 0) {
                $vv['TotalBet'] = calculate($v['diamond']);
                $vv["WalletMoney"] = calculate($v['diamond'] * $yeildVal);
            } else {
                $vv['TotalBet'] = calculate($v['diamond']);
                $vv["WalletMoney"] = 0;
            }

            $t = $v['gameNumb'] . date('_Ymd', strtotime($v["times"]));
            $vv["Lock"] = $t;
            if (isset($game[$v['gameID']][$t][$usrid])) {
                $ii = $game[$v['gameID']][$t][$usrid];
                if ($yeildVal == -1) {
                    $data['results'][$ii]['TotalBet'] = $vv['TotalBet'];
                } else {
                    $data['results'][$ii]['TotalBet'] = bcadd($data['results'][$ii]['TotalBet'], $vv['TotalBet'], 2);
                }

                //$data['results'][$ii]['TotalBet'] = bcadd($data['results'][$ii]['TotalBet'], $vv['TotalBet'], 2);
                $data['results'][$ii]['TotalWin'] = bcadd($data['results'][$ii]['TotalWin'], $vv['TotalWin'], 2);
                $data['results'][$ii]['BeginBlance'] = calculate($v['BeginBlance']);
                continue;
            } else {
                $i++;
                $game[$v['gameID']][$t][$usrid] = $i;
            }

            $vv["UserName"] = isset($users[$v["usrid"]]) ? $users[$v["usrid"]] : '-';
            $vv["tableID"] = $v["tableID"];
            $vv["BeginBlance"] = calculate($v["BeginBlance"]);
            $vv["EndBlance"] = calculate($v["EndBlance"]);
            $vv["PlayTime"] = $v["times"];
            $vv["transtype"] = $v['transtype'];
            $vv["GameName"] = $this->Game->getNameById($v['gameID']);
            empty($vv['GameName']) && $vv["GameName"] = $v['transtype'];
            $data['results'][$i] = $vv;

        }

        foreach ($data['results'] as $k => $v) {
            $t = $v['Lock'];
            if ($t) {
                if ($_usrid) {
                    if (count($game[$v['GameID']][$t]) < 2 || !isset($game[$v['GameID']][$t][$_usrid])) {
                        $i--;
                        unset($data['results'][$k]);
                    }

                } else {
                    if (count($game[$v['GameID']][$t]) < 2) {
                        $i--;
                        unset($data['results'][$k]);
                    }
                }


            }
        }
        $last = '';
        $list = [];
        foreach ($data['results'] as $v) {
            $t = $v['Lock'];
            if ($t) {
                $last != $t && $list[] = ['RecordIndex' => 0];
                $last = $t;
                $list[] = $v;
            }
        }

        $list = array_slice($list, ($page - 1) * $size, $size);

        foreach ($list as &$v) {
            $yeildVal = $this->Game->getYeildValue($v['GameID']);
            if ($yeildVal == -1) {
                $v['TotalWin'] = bcadd($v['TotalWin'], $v['TotalBet'], 2);
            }
        }

        $data['total'] = $i;
        $data['results'] = $list;
        $data['ru'] = $game;
        return $data;
    }

    /*
        public function stat_game_date($cond, $start, $end)
        {
            //$cond['style'] = 0;
            $dates = $this->getDatesBetweenTwoDays($start, $end);
            $num = count($dates);

            $data['results'] = [];
            $ymd = date('Y-m-d');
            for ($i = 0; $i < $num; $i++) {
                $cond['times'] = ['>=' => $dates[$i] . ' 00:00:00', '<=' => $dates[$i] . ' 23:59:59'];
                $key = $this->table . '_c_' . md5(xn_json_encode($cond));
                $row = $this->CacheGet($key);
                if (!$row || is_null($row)) {
                    $list = $this->select($cond, [], 'usrid,diamond,times,BeginBlance,EndBlance,gameID');
                    $userTotalInfo = $this->getZhangDataItemByDate($dates[$i], $list);
                    $row = [
                        'mydate' => $dates[$i],
                        'Game_Bet' => calculate($userTotalInfo['Game_Bet']),
                        'Poker_Bet' => calculate($userTotalInfo['Poker_Bet']),
                        'Game_yield' => $userTotalInfo['Game_yield'],
                        'Game_Win' => calculate($userTotalInfo['Game_Win']),
                        'Poker_Win' => calculate($userTotalInfo['Poker_Win']),
                        'Poker_Pump' => calculate($userTotalInfo['Poker_Pump']),
                        'Poker_yield' => $userTotalInfo['Poker_yield'],
                    ];
                    $data['results'][] = $row;
                    $this->CacheSet($key, $row, $ymd == $dates[$i] ? 60 : 600);
                } else {
                    $data['results'][] = $row;
                }
            }
            return $data['results'];
        }
    */
//
//	public function stat_sum($cond)
//	{
//		$key = $this->table . '_b_' . md5(xn_json_encode($cond));
//		$data = $this->CacheGet($key);
//		if (!$data || is_null($data)) {
//			$list = $this->select($cond, [], 'usrid,style,diamond,times,BeginBlance,EndBlance,gameID');
//			$userTotalInfo = $this->getZhangDataItem($list);
//			$data = [
//				'Game_Bet' => calculate($userTotalInfo['Game_Bet']),
//				'Poker_Bet' => calculate($userTotalInfo['Poker_Bet']),
//				'Game_yield' => $userTotalInfo['Game_yield'],
//				'Game_Win' => calculate($userTotalInfo['Game_Win']),
//				'Poker_Win' => calculate($userTotalInfo['Poker_Win']),
//				'Poker_Pump' => calculate($userTotalInfo['Poker_Pump']),
//				'Poker_yield' => $userTotalInfo['Poker_yield'],
//				'Reg_Ad' => calculate($userTotalInfo['Reg_Ad']),
//				'Active_Ad' => calculate($userTotalInfo['Active_Ad']),
//				'Apply_Ad' => calculate($userTotalInfo['Apply_Ad']),
//				'Plat_Ad' => calculate($userTotalInfo['Plat_Ad']),
//				'Pay_Ad' => calculate($userTotalInfo['Pay_Ad']),
//				'Sg_Ad' => calculate($userTotalInfo['Sg_Ad']),
//				'Sg_Sub' => calculate($userTotalInfo['Sg_Sub']),
//				'Sc_Ad' => calculate($userTotalInfo['Sc_Ad']),
//				'Cash_Ad' => calculate($userTotalInfo['Cash_Ad']),
//				'Cash_C_Ad' => calculate($userTotalInfo['Cash_C_Ad']),
//				'Disable_Ad' => calculate($userTotalInfo['Disable_Ad']),
//				'Sms_Ad' => calculate($userTotalInfo['Sms_Ad']),
//			];
//			$this->CacheSet($key, $data, 60);
//		}
//		return $data;
//	}

    public function ymd_array($start, $end, $key = '')
    {
        $arr = $this->getDatesBetweenTwoDays($start, $end);
        foreach ($arr as &$row) {
            $row = $key . str_replace('-', '', $row);
        }
        $arr = array_unique(array_filter($arr));
        return $arr;
    }

    public function agent_child_list($id, $agent_son, &$child = [])
    {
        $child_list = [];
        if (is_array($id)) {
            foreach ($id as $id_s) {
                is_array($agent_son[$id_s]) && $child_list = array_merge($child_list, $agent_son[$id_s]);
            }
        } else {
            is_array($agent_son[$id]) && $child_list = $agent_son[$id];
        }
        $id = arrlist_values($child_list, 'id');
        if (!empty($id) && is_array($id)) {
            $child = empty($child) ? $child_list : array_merge((array)$child_list, (array)$child);
            $this->agent_child_list($id, $agent_son, $child);
        } else {
            $child = array_merge((array)$child, (array)$child_list);
        }
    }


    public function make_son($agent_array)
    {

        $agent_son = [];
        foreach ($agent_array as $v) {
            !isset($agent_son[$v['id']]) && $agent_son[$v['id']] = [];
            !isset($agent_son[$v['parent_id']]) && $agent_son[$v['parent_id']] = [];
            $agent_son[$v['parent_id']][] = ['id' => $v['id']];
        }
        return $agent_son;
    }

    public function stat_sum_new_usr($start, $end, $usrid)
    {
        $ymd = $this->ymd_array($start, $end);
        $list = $this->UsrAgentMoney->select(['ymd' => $ymd, 'usrid' => $usrid]);
        $data = [
            'Game_Bet' => 0, 'Poker_Bet' => 0, 'Game_Win' => 0,
            'Poker_Win' => 0, 'Poker_Pump' => 0, 'Reg_Ad' => 0, 'Active_Ad' => 0,
            'Apply_Ad' => 0, 'Plat_Ad' => 0, 'Pay_Ad' => 0, 'Sg_Ad' => 0, 'Sg_Sub' => 0,
            'Sc_Ad' => 0, 'Cash_Ad' => 0, 'Cash_C_Ad' => 0, 'Disable_Ad' => 0, 'Sms_Ad' => 0, 'Agent_Ad' => 0];
        foreach ($list as $row) {
            $data['Game_Bet'] = bcadd($data['Game_Bet'], $row['Game_Bet']);
            $data['Poker_Bet'] = bcadd($data['Poker_Bet'], $row['Poker_Bet']);
            $data['Game_Win'] = bcadd($data['Game_Win'], $row['Game_Win']);
            $data['Poker_Win'] = bcadd($data['Poker_Win'], $row['Poker_Win']);
            $data['Poker_Pump'] = bcadd($data['Poker_Pump'], $row['Poker_Pump']);
            $data['Reg_Ad'] = bcadd($data['Reg_Ad'], $row['Reg_Ad']);
            $data['Active_Ad'] = bcadd($data['Active_Ad'], $row['Active_Ad']);
            $data['Apply_Ad'] = bcadd($data['Apply_Ad'], $row['Apply_Ad']);
            $data['Plat_Ad'] = bcadd($data['Plat_Ad'], $row['Plat_Ad']);
            $data['Pay_Ad'] = bcadd($data['Pay_Ad'], $row['Pay_Ad']);
            $data['Sg_Ad'] = bcadd($data['Sg_Ad'], $row['Sg_Ad']);
            $data['Sg_Sub'] = bcadd($data['Sg_Sub'], $row['Sg_Sub']);
            $data['Sc_Ad'] = bcadd($data['Sc_Ad'], $row['Sc_Ad']);
            $data['Cash_Ad'] = bcadd($data['Cash_Ad'], $row['Cash_Ad']);
            $data['Cash_C_Ad'] = bcadd($data['Cash_C_Ad'], $row['Cash_C_Ad']);
            $data['Disable_Ad'] = bcadd($data['Disable_Ad'], $row['Disable_Ad']);
            $data['Sms_Ad'] = bcadd($data['Sms_Ad'], $row['Sms_Ad']);
            $data['Agent_Ad'] = bcadd($data['Agent_Ad'], $row['Agent_Ad']);
        }

        if ($data['Game_Bet'] > 0) {
            $data['Game_yield'] = bcdiv(bcmul($data['Game_Win'], 100), $data['Game_Bet'], 2);
        }

        if ($data['Poker_Bet'] > 0) {
            $data['Poker_yield'] = bcdiv(bcmul($data['Poker_Pump'], 100), $data['Poker_Bet'], 2);
        }

        $_data = [
            'Game_Bet' => calculate($data['Game_Bet']),
            'Poker_Bet' => calculate($data['Poker_Bet']),
            'Game_yield' => $data['Game_yield'],
            'Game_Win' => calculate($data['Game_Win']),
            'Poker_Win' => calculate($data['Poker_Win']),
            'Poker_Pump' => calculate($data['Poker_Pump']),
            'Poker_yield' => $data['Poker_yield'],
            'Reg_Ad' => calculate($data['Reg_Ad']),
            'Active_Ad' => calculate($data['Active_Ad']),
            'Apply_Ad' => calculate($data['Apply_Ad']),
            'Plat_Ad' => calculate($data['Plat_Ad']),
            'Pay_Ad' => calculate($data['Pay_Ad']),
            'Sg_Ad' => calculate($data['Sg_Ad']),
            'Sg_Sub' => calculate($data['Sg_Sub']),
            'Sc_Ad' => calculate($data['Sc_Ad']),
            'Cash_Ad' => calculate($data['Cash_Ad']),
            'Cash_C_Ad' => calculate($data['Cash_C_Ad']),
            'Disable_Ad' => calculate($data['Disable_Ad']),
            'Sms_Ad' => calculate($data['Sms_Ad']),
            'Agent_Ad' => calculate($data['Agent_Ad']),
        ];
        return $_data;
    }

public function stat_sum_new($start, $end, $agent_id)
    {
        $ymd = $this->ymd_array($start, $end);
        $list = $this->UsrAgentMoney->select(['ymd' => $ymd]);
        $data = [
            'Game_Bet' => 0, 'Poker_Bet' => 0, 'Game_Win' => 0,
            'Poker_Win' => 0, 'Poker_Pump' => 0, 'Reg_Ad' => 0, 'Active_Ad' => 0,
            'Apply_Ad' => 0, 'Plat_Ad' => 0, 'Pay_Ad' => 0, 'Sg_Ad' => 0, 'Sg_Sub' => 0,
            'Sc_Ad' => 0, 'Cash_Ad' => 0, 'Cash_C_Ad' => 0, 'Disable_Ad' => 0, 'Sms_Ad' => 0, 'Agent_Ad' => 0];
        foreach ($list as $row) {
                $data['Game_Bet'] = bcadd($data['Game_Bet'], $row['Game_Bet']);
                $data['Poker_Bet'] = bcadd($data['Poker_Bet'], $row['Poker_Bet']);
                $data['Game_Win'] = bcadd($data['Game_Win'], $row['Game_Win']);
                $data['Poker_Win'] = bcadd($data['Poker_Win'], $row['Poker_Win']);
                $data['Poker_Pump'] = bcadd($data['Poker_Pump'], $row['Poker_Pump']);
                $data['Reg_Ad'] = bcadd($data['Reg_Ad'], $row['Reg_Ad']);
                $data['Active_Ad'] = bcadd($data['Active_Ad'], $row['Active_Ad']);
                $data['Apply_Ad'] = bcadd($data['Apply_Ad'], $row['Apply_Ad']);
                $data['Plat_Ad'] = bcadd($data['Plat_Ad'], $row['Plat_Ad']);
                $data['Pay_Ad'] = bcadd($data['Pay_Ad'], $row['Pay_Ad']);
                $data['Sg_Ad'] = bcadd($data['Sg_Ad'], $row['Sg_Ad']);
                $data['Sg_Sub'] = bcadd($data['Sg_Sub'], $row['Sg_Sub']);
                $data['Sc_Ad'] = bcadd($data['Sc_Ad'], $row['Sc_Ad']);
                $data['Cash_Ad'] = bcadd($data['Cash_Ad'], $row['Cash_Ad']);
                $data['Cash_C_Ad'] = bcadd($data['Cash_C_Ad'], $row['Cash_C_Ad']);
                $data['Disable_Ad'] = bcadd($data['Disable_Ad'], $row['Disable_Ad']);
                $data['Sms_Ad'] = bcadd($data['Sms_Ad'], $row['Sms_Ad']);
                $data['Agent_Ad'] = bcadd($data['Agent_Ad'], $row['Agent_Ad']);

        }

        $data['Game_yield'] = $data['Game_Bet'] ? bcdiv(bcmul($data['Game_Win'], 100), $data['Game_Bet'], 2) : 0;
        $data['Poker_yield'] = $data['Poker_Bet'] ? bcdiv(bcmul($data['Poker_Pump'], 100), $data['Poker_Bet'], 2) : 0;

        $_data = [
            'Game_Bet' => calculate($data['Game_Bet']),
            'Poker_Bet' => calculate($data['Poker_Bet']),
            'Game_yield' => $data['Game_yield'],
            'Game_Win' => calculate($data['Game_Win']),
            'Poker_Win' => calculate($data['Poker_Win']),
            'Poker_Pump' => calculate($data['Poker_Pump']),
            'Poker_yield' => $data['Poker_yield'],
            'Reg_Ad' => calculate($data['Reg_Ad']),
            'Active_Ad' => calculate($data['Active_Ad']),
            'Apply_Ad' => calculate($data['Apply_Ad']),
            'Plat_Ad' => calculate($data['Plat_Ad']),
            'Pay_Ad' => calculate($data['Pay_Ad']),
            'Sg_Ad' => calculate($data['Sg_Ad']),
            'Sg_Sub' => calculate($data['Sg_Sub']),
            'Sc_Ad' => calculate($data['Sc_Ad']),
            'Cash_Ad' => calculate($data['Cash_Ad']),
            'Cash_C_Ad' => calculate($data['Cash_C_Ad']),
            'Disable_Ad' => calculate($data['Disable_Ad']),
            'Sms_Ad' => calculate($data['Sms_Ad']),
            'Agent_Ad' => calculate($data['Agent_Ad']),
            'ymd'=>$ymd
        ];
        return $_data;
    }


    public function stat_sum_usr_agent_new($start, $end, $agent_id)
    {
        $ymd = $this->ymd_array($start, $end);

        $list = $this->UsrMoney->select(['ymd' => $ymd]);
        $data = [
            'Game_Bet' => 0, 'Poker_Bet' => 0, 'Game_Win' => 0,
            'Poker_Win' => 0, 'Poker_Pump' => 0, 'Reg_Ad' => 0, 'Active_Ad' => 0,
            'Apply_Ad' => 0, 'Plat_Ad' => 0, 'Pay_Ad' => 0, 'Sg_Ad' => 0, 'Sg_Sub' => 0,
            'Sc_Ad' => 0, 'Cash_Ad' => 0, 'Cash_C_Ad' => 0, 'Disable_Ad' => 0, 'Sms_Ad' => 0, 'Agent_Ad' => 0];
        foreach ($list as $row) {
                $data['Game_Bet'] = bcadd($data['Game_Bet'], $row['Game_Bet']);
                $data['Poker_Bet'] = bcadd($data['Poker_Bet'], $row['Poker_Bet']);
                $data['Game_Win'] = bcadd($data['Game_Win'], $row['Game_Win']);
                $data['Poker_Win'] = bcadd($data['Poker_Win'], $row['Poker_Win']);
                $data['Poker_Pump'] = bcadd($data['Poker_Pump'], $row['Poker_Pump']);
                $data['Reg_Ad'] = bcadd($data['Reg_Ad'], $row['Reg_Ad']);
                $data['Active_Ad'] = bcadd($data['Active_Ad'], $row['Active_Ad']);
                $data['Apply_Ad'] = bcadd($data['Apply_Ad'], $row['Apply_Ad']);
                $data['Plat_Ad'] = bcadd($data['Plat_Ad'], $row['Plat_Ad']);
                $data['Pay_Ad'] = bcadd($data['Pay_Ad'], $row['Pay_Ad']);
                $data['Sg_Ad'] = bcadd($data['Sg_Ad'], $row['Sg_Ad']);
                $data['Sg_Sub'] = bcadd($data['Sg_Sub'], $row['Sg_Sub']);
                $data['Sc_Ad'] = bcadd($data['Sc_Ad'], $row['Sc_Ad']);
                $data['Cash_Ad'] = bcadd($data['Cash_Ad'], $row['Cash_Ad']);
                $data['Cash_C_Ad'] = bcadd($data['Cash_C_Ad'], $row['Cash_C_Ad']);
                $data['Disable_Ad'] = bcadd($data['Disable_Ad'], $row['Disable_Ad']);
                $data['Sms_Ad'] = bcadd($data['Sms_Ad'], $row['Sms_Ad']);
                $data['Agent_Ad'] = bcadd($data['Agent_Ad'], $row['Agent_Ad']);

        }

        $data['Game_yield'] = $data['Game_Bet'] ? bcdiv(bcmul($data['Game_Win'], 100), $data['Game_Bet'], 2) : 0;
        $data['Poker_yield'] = $data['Poker_Bet'] ? bcdiv(bcmul($data['Poker_Pump'], 100), $data['Poker_Bet'], 2) : 0;

        $_data = [
            'Game_Bet' => calculate($data['Game_Bet']),
            'Poker_Bet' => calculate($data['Poker_Bet']),
            'Game_yield' => $data['Game_yield'],
            'Game_Win' => calculate($data['Game_Win']),
            'Poker_Win' => calculate($data['Poker_Win']),
            'Poker_Pump' => calculate($data['Poker_Pump']),
            'Poker_yield' => $data['Poker_yield'],
            'Reg_Ad' => calculate($data['Reg_Ad']),
            'Active_Ad' => calculate($data['Active_Ad']),
            'Apply_Ad' => calculate($data['Apply_Ad']),
            'Plat_Ad' => calculate($data['Plat_Ad']),
            'Pay_Ad' => calculate($data['Pay_Ad']),
            'Sg_Ad' => calculate($data['Sg_Ad']),
            'Sg_Sub' => calculate($data['Sg_Sub']),
            'Sc_Ad' => calculate($data['Sc_Ad']),
            'Cash_Ad' => calculate($data['Cash_Ad']),
            'Cash_C_Ad' => calculate($data['Cash_C_Ad']),
            'Disable_Ad' => calculate($data['Disable_Ad']),
            'Sms_Ad' => calculate($data['Sms_Ad']),
            'Agent_Ad' => calculate($data['Agent_Ad']),
        ];
        return $_data;
    }
    public function stat_sum_agent($start, $end, $agent_id)
    {
        $ymd = $this->ymd_array($start, $end);

        $list = $this->UserAgentMoney->select(['ymd' => $ymd,'agent_id'=>$agent_id]);
        $data = [
            'Game_Bet' => 0, 'Poker_Bet' => 0, 'Game_Win' => 0,
            'Poker_Win' => 0, 'Poker_Pump' => 0, 'Reg_Ad' => 0, 'Active_Ad' => 0,
            'Apply_Ad' => 0, 'Plat_Ad' => 0, 'Pay_Ad' => 0, 'Sg_Ad' => 0, 'Sg_Sub' => 0,
            'Sc_Ad' => 0, 'Cash_Ad' => 0, 'Cash_C_Ad' => 0, 'Disable_Ad' => 0, 'Sms_Ad' => 0, 'Agent_Ad' => 0];
        foreach ($list as $row) {
            $data['Game_Bet'] = bcadd($data['Game_Bet'], $row['Game_Bet']);
            $data['Poker_Bet'] = bcadd($data['Poker_Bet'], $row['Poker_Bet']);
            $data['Game_Win'] = bcadd($data['Game_Win'], $row['Game_Win']);
            $data['Poker_Win'] = bcadd($data['Poker_Win'], $row['Poker_Win']);
            $data['Poker_Pump'] = bcadd($data['Poker_Pump'], $row['Poker_Pump']);
            $data['Reg_Ad'] = bcadd($data['Reg_Ad'], $row['Reg_Ad']);
            $data['Active_Ad'] = bcadd($data['Active_Ad'], $row['Active_Ad']);
            $data['Apply_Ad'] = bcadd($data['Apply_Ad'], $row['Apply_Ad']);
            $data['Plat_Ad'] = bcadd($data['Plat_Ad'], $row['Plat_Ad']);
            $data['Pay_Ad'] = bcadd($data['Pay_Ad'], $row['Pay_Ad']);
            $data['Sg_Ad'] = bcadd($data['Sg_Ad'], $row['Sg_Ad']);
            $data['Sg_Sub'] = bcadd($data['Sg_Sub'], $row['Sg_Sub']);
            $data['Sc_Ad'] = bcadd($data['Sc_Ad'], $row['Sc_Ad']);
            $data['Cash_Ad'] = bcadd($data['Cash_Ad'], $row['Cash_Ad']);
            $data['Cash_C_Ad'] = bcadd($data['Cash_C_Ad'], $row['Cash_C_Ad']);
            $data['Disable_Ad'] = bcadd($data['Disable_Ad'], $row['Disable_Ad']);
            $data['Sms_Ad'] = bcadd($data['Sms_Ad'], $row['Sms_Ad']);
            $data['Agent_Ad'] = bcadd($data['Agent_Ad'], $row['Agent_Ad']);
        }

        $_data = [
            'Game_Bet' => calculate($data['Game_Bet']),
            'Poker_Bet' => calculate($data['Poker_Bet']),
            'Game_Win' => calculate($data['Game_Win']),
            'Poker_Win' => calculate($data['Poker_Win']),
            'Poker_Pump' => calculate($data['Poker_Pump']),
            'Reg_Ad' => calculate($data['Reg_Ad']),
            'Active_Ad' => calculate($data['Active_Ad']),
            'Apply_Ad' => calculate($data['Apply_Ad']),
            'Plat_Ad' => calculate($data['Plat_Ad']),
            'Pay_Ad' => calculate($data['Pay_Ad']),
            'Sg_Ad' => calculate($data['Sg_Ad']),
            'Sg_Sub' => calculate($data['Sg_Sub']),
            'Sc_Ad' => calculate($data['Sc_Ad']),
            'Cash_Ad' => calculate($data['Cash_Ad']),
            'Cash_C_Ad' => calculate($data['Cash_C_Ad']),
            'Disable_Ad' => calculate($data['Disable_Ad']),
            'Sms_Ad' => calculate($data['Sms_Ad']),
            'Agent_Ad' => calculate($data['Agent_Ad']),
        ];
        return $_data;
    }


    public function stat_game_date_new($start, $end, $agent_id, $isuser = 0)
    {
        $dates = $this->getDatesBetweenTwoDays($start, $end);
        $num = count($dates);
        $data['results'] = [];
        for ($i = 0; $i < $num; $i++) {
            //$cond['times'] = ['>=' => $dates[$i] . ' 00:00:00', '<=' => $dates[$i] . ' 23:59:59'];
            if ($isuser == 0) {
                $userTotalInfo = $this->stat_sum_new($dates[$i] . ' 00:00:00', $dates[$i] . ' 23:59:59', $agent_id);
            } else {
                $userTotalInfo = $this->stat_sum_new_usr($dates[$i] . ' 00:00:00', $dates[$i] . ' 23:59:59', $agent_id);
            }

            $row = [
                'mydate' => $dates[$i],
                'Game_Bet' => $userTotalInfo['Game_Bet'],
                'Poker_Bet' => $userTotalInfo['Poker_Bet'],
                'Game_yield' => $userTotalInfo['Game_yield'],
                'Game_Win' => $userTotalInfo['Game_Win'],
                'Agent_ad' => $userTotalInfo['Agent_ad'],
                'Poker_Win' => $userTotalInfo['Poker_Win'],
                'Poker_Pump' => $userTotalInfo['Poker_Pump'],
                'Poker_yield' => $userTotalInfo['Poker_yield'],
            ];
            $data['results'][] = $row;
        }
        return $data['results'];
    }

    public function stat_user_game_date_new($start, $end, $agent_id, $isuser = 0)
    {
        $dates = $this->getDatesBetweenTwoDays($start, $end);
        $num = count($dates);
        $data['results'] = [];
        for ($i = 0; $i < $num; $i++) {
            //$cond['times'] = ['>=' => $dates[$i] . ' 00:00:00', '<=' => $dates[$i] . ' 23:59:59'];
            if ($isuser == 0) {
                $userTotalInfo = $this->stat_sum_new($dates[$i] . ' 00:00:00', $dates[$i] . ' 23:59:59', $agent_id);
            } else {
                $userTotalInfo = $this->stat_sum_new_usr($dates[$i] . ' 00:00:00', $dates[$i] . ' 23:59:59', $agent_id);
            }

            $row = [
                'mydate' => $dates[$i],
                'Game_Bet' => $userTotalInfo['Game_Bet'],
                'Poker_Bet' => $userTotalInfo['Poker_Bet'],
                'Game_yield' => $userTotalInfo['Game_yield'],
                'Game_Win' => $userTotalInfo['Game_Win'],
                'Agent_ad' => $userTotalInfo['Agent_ad'],
                'Poker_Win' => $userTotalInfo['Poker_Win'],
                'Poker_Pump' => $userTotalInfo['Poker_Pump'],
                'Poker_yield' => $userTotalInfo['Poker_yield'],
            ];
            $data['results'][] = $row;
        }
        return $data['results'];
    }
    public function task_stat_sum($start)
    {
        $ymd = date('Ymd', $start);
        $cond['times'] = ['>=' => date('Y-m-d 00:00:00', $start), '<=' => date('Y-m-d 23:59:59', $start)];
        $list = $this->select($cond, [], 'usrid,style,diamond,times,BeginBlance,EndBlance,gameID,action_id');
        $userlist = arrlist_group($list, 'usrid');

        $users = $this->Usr->select(['usrid' => arrlist_values($list, 'usrid')], [], 'usrid,agent_id');
        $users = arrlist_key_values($users, 'usrid', 'agent_id');
        $usrmoney = $this->UsrMoney->select(['ymd' => $ymd]);
        $usrmoney = arrlist_key_values($usrmoney, 'usrid', 'ymd');
        foreach ($userlist as $usrid => $row) {
            $userTotalInfo = $this->getZhangDataItem($row);
            $user = [
                'Game_Bet' => $userTotalInfo['Game_Bet'],
                'Poker_Bet' => $userTotalInfo['Poker_Bet'],
                'Game_Win' => $userTotalInfo['Game_Win'],
                'Poker_Win' => $userTotalInfo['Poker_Win'],
                'Poker_Pump' => $userTotalInfo['Poker_Pump'],
                'Reg_Ad' => $userTotalInfo['Reg_Ad'],
                'Active_Ad' => $userTotalInfo['Active_Ad'],
                'Apply_Ad' => $userTotalInfo['Apply_Ad'],
                'Plat_Ad' => $userTotalInfo['Plat_Ad'],
                'Pay_Ad' => $userTotalInfo['Pay_Ad'],
                'Sg_Ad' => $userTotalInfo['Sg_Ad'],
                'Sg_Sub' => $userTotalInfo['Sg_Sub'],
                'Sc_Ad' => $userTotalInfo['Sc_Ad'],
                'Cash_Ad' => $userTotalInfo['Cash_Ad'],
                'Cash_C_Ad' => $userTotalInfo['Cash_C_Ad'],
                'Disable_Ad' => $userTotalInfo['Disable_Ad'],
                'Sms_Ad' => $userTotalInfo['Sms_Ad'],
                'Agent_Ad' => $userTotalInfo['Agent_Ad'],
            ];

            if ($ymd == date('Ymd')) {
                $agent = $this->User->select([], [], 'id,parent_id,RoleID');
                $user['agent_id'] = $users[$usrid];
                $key = 'agent_' . $ymd;
                $k = $this->Kv->read($key);
                if (!$k) {
                    $this->Kv->insert($key, $agent);
                } else {
                    $this->Kv->update($key, $agent);
                }
            }

            if (empty($usrmoney[$usrid])) {
                $user['ymd'] = $ymd;
                $user['usrid'] = $usrid;
                $user['agent_id'] = $users[$usrid];
                $user['update_at'] = $user['created_at'] = time();
                $this->UsrMoney->insert($user);
            } else {
                $user['update_at'] = time();
                $this->UsrMoney->update(['ymd' => $ymd, 'usrid' => $usrid], $user);
            }
        }

        $usrmoney = $this->UserMoney->select(['ymd' => $ymd]);
        $usrmoney = arrlist_key_values($usrmoney, 'agent_id', 'ymd');
        $agentlist = arrlist_group($list, 'action_id');

        foreach ($agentlist as $usrid => $row) {
            $userTotalInfo = $this->getZhangDataItem($row);
            $user = [
                'Game_Bet' => $userTotalInfo['Game_Bet'],
                'Poker_Bet' => $userTotalInfo['Poker_Bet'],
                'Game_Win' => $userTotalInfo['Game_Win'],
                'Poker_Win' => $userTotalInfo['Poker_Win'],
                'Poker_Pump' => $userTotalInfo['Poker_Pump'],
                'Reg_Ad' => $userTotalInfo['Reg_Ad'],
                'Active_Ad' => $userTotalInfo['Active_Ad'],
                'Apply_Ad' => $userTotalInfo['Apply_Ad'],
                'Plat_Ad' => $userTotalInfo['Plat_Ad'],
                'Pay_Ad' => $userTotalInfo['Pay_Ad'],
                'Sg_Ad' => $userTotalInfo['Sg_Ad'],
                'Sg_Sub' => $userTotalInfo['Sg_Sub'],
                'Sc_Ad' => $userTotalInfo['Sc_Ad'],
                'Cash_Ad' => $userTotalInfo['Cash_Ad'],
                'Cash_C_Ad' => $userTotalInfo['Cash_C_Ad'],
                'Disable_Ad' => $userTotalInfo['Disable_Ad'],
                'Sms_Ad' => $userTotalInfo['Sms_Ad'],
                'Agent_Ad' => $userTotalInfo['Agent_Ad'],
            ];

            if (empty($usrmoney[$usrid])) {
                $user['ymd'] = $ymd;
                $user['agent_id'] = $usrid;
                $user['created_at'] = time();
                $this->UserMoney->insert($user);
            } else {
                $user['update_at'] = time();
                $this->UserMoney->update(['ymd' => $ymd, 'agent_id' => $usrid], $user);
            }
        }

    }


    /**
     * @param $start
     */
    public function task_useragent_sum($start)
    {
        $ymd = date('Ymd', $start);
        $cond['times'] = ['>=' => date('Y-m-d 00:00:00', $start), '<=' => date('Y-m-d 23:59:59', $start)];
        $list = $this->select($cond, [], 'usrid,style,diamond,times,BeginBlance,EndBlance,gameID,action_id');
        $userlist = arrlist_group($list, 'usrid');
        $condParents['guid'] =['>'=>0];

        $users = $this->User->select($condParents, [], 'id,parent_id,guid');
        $parentsGuid = arrlist_key_values($users, 'id', 'guid');
        $users = arrlist_key_values($users, 'guid', 'parent_id');

        $usrmoney = $this->UsrAgentMoney->select(['ymd' => $ymd]);
        $usrmoney = arrlist_key_values($usrmoney, 'usrid', 'ymd');
        $insertArr = [];
        //生成/更新统计
        foreach ($userlist as $usrid => $row) {
            $userTotalInfo = $this->getUserAgentZhangDataItem($row);
            $user = [
                'Game_Bet' => $userTotalInfo['Game_Bet'],
                'Poker_Bet' => $userTotalInfo['Poker_Bet'],
                'Game_Win' => $userTotalInfo['Game_Win'],
                'Poker_Win' => $userTotalInfo['Poker_Win'],
                'Poker_Pump' => $userTotalInfo['Poker_Pump'],
                'Reg_Ad' => $userTotalInfo['Reg_Ad'],
                'Active_Ad' => $userTotalInfo['Active_Ad'],
                'Apply_Ad' => $userTotalInfo['Apply_Ad'],
                'Plat_Ad' => $userTotalInfo['Plat_Ad'],
                'Pay_Ad' => $userTotalInfo['Pay_Ad'],
                'Sg_Ad' => $userTotalInfo['Sg_Ad'],
                'Sg_Sub' => $userTotalInfo['Sg_Sub'],
                'Sc_Ad' => $userTotalInfo['Sc_Ad'],
                'Cash_Ad' => $userTotalInfo['Cash_Ad'],
                'Cash_C_Ad' => $userTotalInfo['Cash_C_Ad'],
                'Disable_Ad' => $userTotalInfo['Disable_Ad'],
                'Sms_Ad' => $userTotalInfo['Sms_Ad'],
                'Agent_Ad' => $userTotalInfo['Agent_Ad'],

            ];
            if (empty($usrmoney[$usrid])) {
                $user['ymd'] = $ymd;
                $user['usrid'] = $usrid;
                $user['agent_id'] = $parentsGuid[$users[$usrid]];
                $user['update_at'] = $user['created_at'] = time();
                $this->UsrAgentMoney->insert($user);
                $insertArr[]= $user;
            } else {
                $user['update_at'] = time();
                $this->UsrAgentMoney->update(['ymd' => $ymd, 'usrid' => $usrid], $user);
            }
        }
        //更新其他汇总信息
        $this->task_update_usr_monney_total($start);
        xn_log('更新其他汇总信息 '.date('Y-m-d H:i:s'),'task_update_usr_monney_total');
        //检查流水达标奖励
        $this->task_update_user_agent_friend_bet_reward($start);
        xn_log('检查流水达标奖励 '.date('Y-m-d H:i:s'),'task_update_user_agent_friend_bet_reward');
    }



    /*
     * 更新汇总统计
     */
    public function  task_update_usr_monney_total($start)
    {
        $ymd = date('Ymd', $start);
        $usrmoneyList = $this->UsrAgentMoney->select(['ymd' => $ymd,'status'=>0]);
        $usrmoney = arrlist_key_values($usrmoneyList, 'usrid', 'ymd');
        $usrmoneyListGroup = arrlist_group($usrmoneyList,'agent_id');

        $condNewuser['regtime'] = ['>=' => date('Y-m-d 00:00:00', $start), '<=' =>date('Y-m-d 23:59:59', $start)];
        $dayNewUserList =  $this->Usr->select($condNewuser, [], 'usrid,agent_id,parent_id');
        $newUserIds = arrlist_values($dayNewUserList,'usrid');
        $toUpdateArr = [];
      foreach ($usrmoneyListGroup as $agent_id => $row)
      {
        $Game_Bet = arrlist_sum($row,'Game_Bet');
        $Poker_Bet = arrlist_sum($row,'Poker_Bet');
        $Total_Bet = $Game_Bet+$Poker_Bet;
        $Log_Reward = $Total_Bet*0.005;
        $update['Log_Reward'] = $Log_Reward;
        $Reg_Vaild_User = 0;
        $Reg_User = 0;
        //注册人数
        foreach ($row as $item)
        {
            //新增付费
            if($item['Pay_Ad']>0||$item['Sg_Ad']>0)
            {
                if(in_array($item['usrid'],$newUserIds))
                {
                    $Reg_Vaild_User += 1;
                }
            }
            if($item['Game_Bet']>0||$item['Poker_Bet']>0)
            {
                $Reg_User += 1;
            }
        }

          $update['Reg_Vaild_User'] = $Reg_Vaild_User;
          $update['Reg_User'] = $Reg_User;
          if (empty($usrmoney[$agent_id])) {
              if($agent_id>0)
              {
                  $update['ymd'] = $ymd;
                  $update['usrid'] = $agent_id;
                  $update['created_at'] = time();
                  $update['update_at'] = time();
                  $this->UsrAgentMoney->insert($update);
              }
          }
          else{
              $toUpdateArr[] = $update;
              $this->UsrAgentMoney->update(['usrid'=>$agent_id,'ymd'=>$ymd],$update);
          }
      }

        //更新自己的流水奖励
        foreach ($usrmoneyList as $item)
        {
            $othetUpdate['Self_Log_Reward'] = ($item['Game_Bet'] +$item['Poker_Bet'])*0.005 ;
            $this->UsrAgentMoney->update(['usrid'=>$item['usrid'],'ymd'=>$ymd],$othetUpdate);
        }
    }

    /*
     * 发送奖励到账户
     */
    public function task_usr_agent_money_to_account($start)
    {
        $ymd = date('Ymd', $start);
        $usrmoneyListOk = $this->UsrAgentMoney->select(['ymd' => $ymd,'status'=>[0,1]]);
        foreach ($usrmoneyListOk as $item)
        {
            $total = $item['Reg_Reward']+$item['Log_Reward']+$item['Self_Log_Reward']+$item['Friend_Bet_Reward'];
            $update['Total_Reward+'] = $total;;
            $update['Cur_Reward+'] = $total;
            $update['Game_Bet+'] = $item['Game_Bet'];
            $update['Poker_Bet+'] = $item['Poker_Bet'];
            $update['Reg_Reward+'] = $item['Reg_Reward'];
            $update['Log_Reward+'] = $item['Log_Reward'];
            $update['Self_Log_Reward+'] = $item['Self_Log_Reward'];
            $update['Last_Reg_User+'] = $item['Reg_User'];
            $this->User->update(['guid'=>$item['usrid']],$update);
            $this->UsrAgentMoney->update(['usrid'=>$item['usrid'],'ymd'=>$ymd],['Status'=>2]);
        }
    }


    public function getCurrentUserAgentMoneyInfo($username,$start)
    {
        $ymd = date('Ymd', $start);
        $info['Total_Reward'] = 0;
        $info['Cur_Reward'] = 0;
        $info['Game_Bet'] = 0;
        $info['Poker_Bet'] = 0;
        $info['Log_Reward'] = 0;
        $info['Reg_Reward'] = 0;
        $result = $this->UsrAgentMoney->read(['ymd' => $ymd,'usrid'=>$username]);

        $key = 'UserAgentMoneyInfo_'.$username;
        $cachData = $this->User->CacheGet($key);
        if($cachData)
        {
            $info =  $cachData;
        }
        else{
            if(isset($result['usrid']))
            {
                $info['Total_Reward'] = $result['Log_Reward']+$result['Reg_Reward']+$result['Self_Log_Reward'];
                $info['Cur_Reward'] = $result['Log_Reward']+$result['Reg_Reward']+$result['Self_Log_Reward'];
                $info['Game_Bet'] = $result['Game_Bet'];
                $info['Poker_Bet'] = $result['Poker_Bet'];
                $info['Log_Reward'] = $result['Log_Reward'];
                $info['Reg_Reward'] =  $result['Reg_Reward'];
                $info['Self_Log_Reward'] = $result['Self_Log_Reward'];
                $this->User->CacheSet($key, $info, 10);
                return $info ;
            }
        }
        return $info ;

    }

    public function getZhangDataItem($recorderList)
    {

        $Game_Bet = 0;
        $Game_Win = 0;
        $Poker_Bet = 0;
        $Poker_Win = 0;
        $Poker_Pump = 0;
        $Game_yield = 0;
        $Poker_yield = 0;
        $Reg_Ad = 0;
        $Active_Ad = 0;
        $Apply_Ad = 0;
        $Plat_Ad = 0;
        $Pay_Ad = 0;
        $Sg_Ad = 0;
        $Sg_Sub = 0;
        $Sc_Ad = 0;
        $Cash_Ad = 0;
        $Agent_Ad = 0;
        $Cash_C_Ad = 0;
        $Disable_Ad = 0;
        $Sms_Ad = 0;
        $Clear = 0;
        foreach ($recorderList as $recorderItem) {
            $chips = $recorderItem['EndBlance'] - $recorderItem['BeginBlance'];
            if ($recorderItem['gameID'] != 0 && ($recorderItem['gameID'] != 1000002 || ($recorderItem['gameID'] == 1000002 && $recorderItem['diamond'] != 0)) && $recorderItem['gameID'] != 100001) {
                $yeildVal = $this->Game->getYeildValue($recorderItem['gameID']);
                if ($yeildVal <= 0) {
                    $Game_Win = bcadd($Game_Win, $chips);
                    $Game_Bet = bcadd($Game_Bet, $recorderItem['diamond']);
                } else if ($yeildVal > 0) {
                    $Poker_Win = bcadd($Poker_Win, $chips);
                    $Poker_Bet = bcadd($Poker_Bet, $recorderItem['diamond']);
                    $Poker_Pump = bcadd($Poker_Pump, $recorderItem['diamond'] == 0 ? $chips * $yeildVal : 0);
                }
            } else if ($recorderItem['gameID'] == 1000002) {
                $Plat_Ad = bcadd($Plat_Ad, $chips);
            } else {
                switch ($recorderItem['style']) {
                    case 25://注册送
                        $Reg_Ad = bcadd($Reg_Ad, $chips);
                        break;
                    case 29://申请送
                        $Apply_Ad = bcadd($Apply_Ad, $chips);
                        break;
                    case 35://系统送
                        //case 0://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 1://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 2://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 3://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 4://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 5://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 6://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 8://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 9://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 10://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 17:
                        $Plat_Ad = bcadd($Plat_Ad, $chips);
                        break;
                    case 36://活动送
                        $Active_Ad = bcadd($Active_Ad, $chips);
                        break;
                    case 37://提现
                        $Cash_Ad = bcadd($Cash_Ad, $chips);
                        break;
                    case 38://取消提现
                        $Cash_C_Ad = bcadd($Cash_C_Ad, $chips);
                        break;
                    case 39://禁用提现
                        $Disable_Ad = bcadd($Disable_Ad, $chips);
                        break;
                    case 50://短信送
                        $Sms_Ad = bcadd($Sms_Ad, $chips);
                        $Active_Ad = bcadd($Active_Ad, $chips);
                        break;
                    case 51://手工上分
                        $Sg_Ad = bcadd($Sg_Ad, $chips);
                        $Pay_Ad = bcadd($Pay_Ad, $chips);
                        break;
                    case 52://手工减分
                    case 53://手工减分
                        $Sg_Sub = bcadd($Sg_Sub, $chips);
                        break;
                    case 71://代理结算
                        $Agent_Ad = bcadd($Agent_Ad, $chips);
                        break;
                    case 61://商城上分
                        $Sc_Ad = bcadd($Sc_Ad, $chips);
                        $Pay_Ad = bcadd($Pay_Ad, $chips);
                        break;
                    case 114://商城上分
                        $Clear = bcadd($Clear, $chips);
                        break;
                }
            }
        }

        if ($Game_Bet > 0) {
            $Game_yield = bcdiv(bcmul(-$Game_Win, 100), $Game_Bet, 2);
        }

        if ($Poker_Bet > 0) {
            $Poker_yield = bcdiv(bcmul($Poker_Pump, 100), $Poker_Bet, 2);
        }

        $list = [
            'Game_Bet' => $Game_Bet,
            'Game_Win' => -$Game_Win,
            'Poker_Bet' => $Poker_Bet,
            'Poker_Win' => -$Poker_Win,
            'Poker_Pump' => $Poker_Pump,
            'Game_yield' => $Game_yield,
            'Poker_yield' => $Poker_yield,
            'Reg_Ad' => $Reg_Ad,
            'Active_Ad' => $Active_Ad,
            'Apply_Ad' => $Apply_Ad,
            'Plat_Ad' => $Plat_Ad,
            'Pay_Ad' => $Pay_Ad,
            'Sg_Ad' => $Sg_Ad,
            'Sg_Sub' => $Sg_Sub,
            'Sc_Ad' => $Sc_Ad,
            'Cash_Ad' => $Cash_Ad,
            'Cash_C_Ad' => $Cash_C_Ad,
            'Disable_Ad' => $Disable_Ad,
            'Sms_Ad' => $Sms_Ad,
            'Agent_Ad' => $Agent_Ad,
        ];
        return $list;
    }

    public function getDatesBetweenTwoDays($startDate, $endDate, $sortType = 0)
    {
        $dates = [];
        $start_time = strtotime($startDate);
        $end_time = strtotime($endDate);

        if ($start_time > $end_time) {
            //如果开始日期大于结束日期，直接return 防止下面的循环出现死循环
            return $dates;
        } else if ($startDate == $endDate) {
            //开始日期与结束日期是同一天时
            $dates[] = $startDate;
            return $dates;
        } else {
            for ($i = $start_time; $i <= $end_time; $i += 86400) {
                $dates[] = date('Y-m-d', $i);
            }
            if ($sortType == 1) {
                return array_reverse($dates);
            }
            return $dates;
        }
    }
    public function getUserAgentZhangDataItem($recorderList)
    {

        $Game_Bet = 0;
        $Game_Win = 0;
        $Poker_Bet = 0;
        $Poker_Win = 0;
        $Poker_Pump = 0;
        $Game_yield = 0;
        $Poker_yield = 0;
        $Reg_Ad = 0;
        $Active_Ad = 0;
        $Apply_Ad = 0;
        $Plat_Ad = 0;
        $Pay_Ad = 0;
        $Sg_Ad = 0;
        $Sg_Sub = 0;
        $Sc_Ad = 0;
        $Cash_Ad = 0;
        $Agent_Ad = 0;
        $Cash_C_Ad = 0;
        $Disable_Ad = 0;
        $Sms_Ad = 0;
        $Clear = 0;


        foreach ($recorderList as $recorderItem) {
            $chips = $recorderItem['EndBlance'] - $recorderItem['BeginBlance'];
            if ($recorderItem['gameID'] != 0 && ($recorderItem['gameID'] != 1000002 || ($recorderItem['gameID'] == 1000002 && $recorderItem['diamond'] != 0)) && $recorderItem['gameID'] != 100001) {
                $yeildVal = $this->Game->getYeildValue($recorderItem['gameID']);
                if ($yeildVal <= 0) {
                    $Game_Win = bcadd($Game_Win, $chips);
                    $Game_Bet = bcadd($Game_Bet, $recorderItem['diamond']);
                } else if ($yeildVal > 0) {
                    $Poker_Win = bcadd($Poker_Win, $chips);
                    $Poker_Bet = bcadd($Poker_Bet, $recorderItem['diamond']);
                    $Poker_Pump = bcadd($Poker_Pump, $recorderItem['diamond'] == 0 ? $chips * $yeildVal : 0);
                }
            } else if ($recorderItem['gameID'] == 1000002) {
                $Plat_Ad = bcadd($Plat_Ad, $chips);
            } else {
                switch ($recorderItem['style']) {
                    case 25://注册送
                        $Reg_Ad = bcadd($Reg_Ad, $chips);
                        break;
                    case 29://申请送
                        $Apply_Ad = bcadd($Apply_Ad, $chips);
                        break;
                    case 35://系统送
                        //case 0://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 1://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 2://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 3://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 4://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 5://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 6://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 8://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 9://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 10://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    case 17:
                        $Plat_Ad = bcadd($Plat_Ad, $chips);
                        break;
                    case 36://活动送
                        $Active_Ad = bcadd($Active_Ad, $chips);
                        break;
                    case 37://提现
                        $Cash_Ad = bcadd($Cash_Ad, $chips);
                        break;
                    case 38://取消提现
                        $Cash_C_Ad = bcadd($Cash_C_Ad, $chips);
                        break;
                    case 39://禁用提现
                        $Disable_Ad = bcadd($Disable_Ad, $chips);
                        break;
                    case 50://短信送
                        $Sms_Ad = bcadd($Sms_Ad, $chips);
                        $Active_Ad = bcadd($Active_Ad, $chips);
                        break;
                    case 51://手工上分
                        $Sg_Ad = bcadd($Sg_Ad, $chips);
                        $Pay_Ad = bcadd($Pay_Ad, $chips);
                        break;
                    case 52://手工减分
                    case 53://手工减分
                        $Sg_Sub = bcadd($Sg_Sub, $chips);
                        break;
                    case 71://代理结算
                        $Agent_Ad = bcadd($Agent_Ad, $chips);
                        break;
                    case 61://商城上分
                        $Sc_Ad = bcadd($Sc_Ad, $chips);
                        $Pay_Ad = bcadd($Pay_Ad, $chips);
                        break;
                    case 114://商城上分
                        $Clear = bcadd($Clear, $chips);
                        break;
                }
            }
        }

        if ($Game_Bet > 0) {
            $Game_yield = bcdiv(bcmul(-$Game_Win, 100), $Game_Bet, 2);
        }

        if ($Poker_Bet > 0) {
            $Poker_yield = bcdiv(bcmul($Poker_Pump, 100), $Poker_Bet, 2);
        }

        $list = [
            'Game_Bet' => $Game_Bet,
            'Game_Win' => -$Game_Win,
            'Poker_Bet' => $Poker_Bet,
            'Poker_Win' => -$Poker_Win,
            'Poker_Pump' => $Poker_Pump,
            'Game_yield' => $Game_yield,
            'Poker_yield' => $Poker_yield,
            'Reg_Ad' => $Reg_Ad,
            'Active_Ad' => $Active_Ad,
            'Apply_Ad' => $Apply_Ad,
            'Plat_Ad' => $Plat_Ad,
            'Pay_Ad' => $Pay_Ad,
            'Sg_Ad' => $Sg_Ad,
            'Sg_Sub' => $Sg_Sub,
            'Sc_Ad' => $Sc_Ad,
            'Cash_Ad' => $Cash_Ad,
            'Cash_C_Ad' => $Cash_C_Ad,
            'Disable_Ad' => $Disable_Ad,
            'Sms_Ad' => $Sms_Ad,
            'Agent_Ad' => $Agent_Ad,

        ];
        return $list;
    }
    // hook model_moneytrans_end.php
    public function task_usr_agent_user_reg_count($input)
    {
        $start = time();
        $condNewuser['a.regtime'] = ['>=' => date('Y-m-d 00:00:00', $start), '<=' =>date('Y-m-d 23:59:59', $start)];
        $condNewuser['a.ai'] = 0;
        $list= $this->Usr->GetWithList([['table' => $this->User->table . ' b', 'join' => 'left', 'and' => 'b.id = a.agent_id']], $condNewuser, ['a.offline_time' => -1], 1, 10000000,'a.usrid,a.agent_id,b.Last_Reg_User');
        $users = $list['results'];
        $arrGroup = arrlist_group($users,'agent_id');
        foreach ($users as $item)
        {
            $this->User->update(['id'=>$item['agent_id']],['Reg_User'=>$item['Last_Reg_User']+count($arrGroup[$item['agent_id']])]);
        }
    }



    public  function  taskLogRewardStatusUpdate($start)
    {
        $ymd = date('Ymd', $start);
        $usrmoneyList = $this->UsrAgentMoney->select(['ymd' => $ymd,'status'=>0]);
        $usrmoney = arrlist_key_values($usrmoneyList, 'usrid', 'ymd');
        $usrmoneyListGroup = arrlist_group($usrmoneyList,'agent_id');

        $start = time();
        $condNewuser['a.onlie'] = ['>'=>0];
        $list= $this->Usr->GetWithList([['table' => $this->User->table . ' b', 'join' => 'left', 'and' => 'b.guid = a.usrid']], $condNewuser, ['a.offline_time' => -1], 1, 10000000,'a.usrid,a.agent_id,b.Last_Reg_User,b.parent_id,b.guid');
        $users = $list['results'];
        foreach ($users as $item)
        {
            //流水达到200奖励上级
            if($item['Log_Reward_Status']==0 && ($item['Game_Bet']+$item['Poker_Bet'])>=200000)
            {
                $parent_id  = $item['parent_id'];
                if($parent_id > 0) {
                    $parentInfo =  $this->User->read(['id'=>$parent_id]);
                    if(!empty($parentInfo['guid'])&& $parentInfo['guid']>0)
                    {
                        $parentParentInfo = $this->User->read(['id'=>$parentInfo['parent_id']]);
                        $parentParentUsrid = 0 ;
                        if($parentParentInfo)
                        {

                            if($parentParentInfo['guid'] > 0)
                            {
                                $parentParentUsrid = $parentParentInfo['guid'];
                            }
                        }
                        if(empty($usrmoney[$parentInfo['guid']]))
                        {
                            if($parent_id)
                            {
                                $insert['ymd'] = $ymd;
                                $insert['usrid'] = $parentInfo['guid'];
                                $insert['agent_id'] = $parentParentUsrid;
                                $insert['created_at'] = time();
                                $insert['update_at'] = time();
                                $this->UsrAgentMoney->insert($insert);
                            }
                        }
                        else{
                            $update['Friend_Bet_Reward+'] = 30000;
                            $this->UsrAgentMoney->update(['usrid'=>$parentInfo['guid'],'ymd'=>$ymd],$update);
                        }
                    }

                }


            }
        }

    }


    public function task_update_user_agent_friend_bet_reward($start)
    {

        $ymd = date('Ymd', $start);
        $usrmoneyList = $this->UsrAgentMoney->select(['ymd' => $ymd,'status'=>0]);
        $usrmoney= arrlist_key_values($usrmoneyList,'usrid','ymd');
        $usrmoneyParent = arrlist_key_values($usrmoneyList, 'usrid', 'agent_id');
        $usrmoneyGroup = arrlist_group($usrmoneyList,'usrid' );
        $condNewuser['a.last_login_time'] = ['>'=>0];
        $condNewuser['b.Log_Reward_Status'] = 0;
        $todayBetUserIds = arrlist_values($usrmoneyList,'usrid');
        $list= $this->Usr->GetWithList([['table' => $this->User->table . ' b', 'join' => 'left', 'and' => 'b.guid = a.usrid']], $condNewuser, ['a.offline_time' => -1], 1, 10000000,'a.usrid,a.agent_id,b.Log_Reward_Status,b.parent_id,b.guid,b.Game_Bet,b.Poker_Bet');
        $users = $list['results'];
        $insertArr =[];
        $upateArr = [];
        foreach ($users as $item)
        {
            if(in_array($item['usrid'],$todayBetUserIds))
            {
                //流水达到200奖励上级
                $Game_Bet = arrlist_sum($usrmoneyGroup[$item['usrid']],'Game_Bet');
                $Poker_Bet = arrlist_sum($usrmoneyGroup[$item['usrid']],'Poker_Bet');
                $totalBet = $Game_Bet+$Poker_Bet +$item['Game_Bet'] +$item['Poker_Bet'];
                $parent_id = 0;
                $a =[
                    'userid'=>$item['usrid'],
                    'Game_Bet'=>$Game_Bet,
                    'Poker_Bet'=>$Poker_Bet,
                    'totalBet'=>$totalBet,
                ];

                if(!empty($usrmoneyParent[$item['usrid']]))
                {
                    $a['parent_id'] = $parent_id;
                    $parent_id = $usrmoneyParent[$item['usrid']];;
                }
                if($totalBet>=200000)
                {
                    if($parent_id>0)
                    {
                        if(empty($usrmoney[$parent_id]))
                        {

                            $parentInfo = $this->User->read(['guid'=>$parent_id]);
                            if($parentInfo)
                            {
                                $parentParentId = $parentInfo['parent_id'];
                                if($parentParentId>0)
                                {
                                    $parentParentInfo= $this->User->read(['id'=>$parentParentId]);
                                    if($parentParentInfo)
                                    {
                                        $insert['ymd'] = $ymd;
                                        $insert['usrid'] = $parent_id;
                                        if(!empty($parentParentInfo['guid']))
                                        {
                                            $insert['agent_id'] = $parentParentInfo['guid'];
                                        }
                                        $insert['created_at'] = time();
                                        $insert['update_at'] = time();
                                        $insert['Reg_Reward'] = 30000;
                                        $this->UsrAgentMoney->insert($insert);
                                        $this->User->update(['guid'=>$item['usrid']],['Log_Reward_Status'=>1]);
                                        $insertArr[] = $insert;
                                    }
                                }
                            }
                        }
                        else{
                            $update['Reg_Reward+'] = 30000;
                            $update['update_at'] = time();
                            $this->UsrAgentMoney->update(['usrid'=>$parent_id,'ymd'=>$ymd],$update);
                            $this->User->update(['guid'=>$item['usrid']],['Log_Reward_Status'=>1]);
                            $upateArr[] = array_merge($update,['usrid'=>$parent_id,'ymd'=>$ymd],['guid'=>$item['usrid']]) ;

                        }
                    }
                }

            }
        }
    }
}

?>