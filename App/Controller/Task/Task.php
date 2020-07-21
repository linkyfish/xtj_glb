<?php

namespace Task;

use Ctrl\Controller;

// hook task_task_use.php

Class Task extends Controller
{
//
//    public function __construct($server, $route, \Request $request, $response)
//    {
//        parent::__construct($server, $route, $request, $response);
//
//    }
	/**
	 * 汇总过去几分钟数据
	 */

	public function redo($data){
		$list = $this->MoneyTrans->find_one([],['id'=>1],'times');
		$start = $list['times'];
		$end = date('Y-m-d H:i:s',time()-86400);
		$date = $this->MoneyTrans->getDatesBetweenTwoDays($start,$end);
		$array=[];
		foreach ($date as $v){
			$array[]=	$this->moneytrans_minute(['start' => strtotime($v . ' 10:10:10')]);
		}
		return $start.' - '.$end.'修复';
	}

	public function moneytrans_minute($data)
	{

		$start = time();
		$end = isset($data['start']) ? $data['start'] : time();
		$this->MoneyTrans->task_stat_sum($end);
		//xn_log($end.' 执行完毕 耗时'.(time()-$start).'秒','moneytrans_minute');
		return date('YmdHis', $end) . ' 执行完毕 耗时' . (time() - $start) . '秒';

	}

    public function task_useragent_sum($data)
    {
        $start = time();
        $end = isset($data['start']) ? $data['start'] : time();
        $this->MoneyTransAgent->task_useragent_sum($end);
        return date('YmdHis', $end) . ' 执行完毕 耗时' . (time() - $start) . '秒';
    }


    /**
     * 发送奖励到账户
     */
    public function task_usr_agent_money_to_account($data)
    {
        $start = time();
        $end = isset($data['start']) ? $data['start'] : time();
        $this->MoneyTransAgent->task_usr_agent_money_to_account($end);
        return date('YmdHis', $end) . ' 执行完毕 耗时' . (time() - $start) . '秒';
    }


    /**
     * 更新用户注册个数
     */
    public function task_usr_agent_user_reg_count($data)
    {
        $start = time();
        $end = isset($data['start']) ? $data['start'] : time();
        $this->MoneyTransAgent->task_usr_agent_user_reg_count($end);
        return date('YmdHis', $end) . ' 执行完毕 耗时' . (time() - $start) . '秒';
    }

    public function task_update_user_agent_friend_bet_reward($data)
    {
        $start = time();
        $end = isset($data['start']) ? $data['start'] : time();
        $this->MoneyTransAgent->task_update_user_agent_friend_bet_reward($end);
        return date('YmdHis', $end) . ' 执行完毕 耗时' . (time() - $start) . '秒';
    }

    /*
 * 检查流水奖励是否达标
 */
    public function  taskLogRewardStatusUpdate($data)
    {
        $start = time();
        $end = isset($data['start']) ? $data['start'] : time();
        $this->MoneyTransAgent->taskLogRewardStatusUpdate($end);
        return date('YmdHis', $end) . ' 执行完毕 耗时' . (time() - $start) . '秒';

    }

    /**
	 * (定时插入跑马灯表)以正常推送公告
	 */
	public function pushAd()
	{
		$this->AdCopy->delete(['endtime' => ['<=' => date("Y-m-d H:i:s")]]);
		$list = $this->AdCopy->select(['endtime' => ['>=' => date("Y-m-d H:i:s")]], [], '*');
		foreach ($list as $key => $v) {
			$this->Ad->insert($v);
		}
	}


	/**
	 * 定时更新用户单控
	 */
	public function updateUserControl()
	{
		$cond['online'] = ['>' => 0];
		$cond['ai'] = 0;
		$userList = $this->Usr->select($cond);
		$lostList = $this->LostControl->select();
		$winList = $this->WinControl->select();
		$superControlList = $this->SuperControlList->select();

		foreach ($userList as $user) {
			$subVal = 0;
			//玩家输钱
			$value = calculate($user['total_win']);// $total_cash-($chips+$total_uppoints);
			//及时更新
			$port = $this->RoomBank->getGameHttpPortById($user['roomid']);
			$update_single_url = 'http://' . _CONF('gameServerIp') . ':' . $port . '/' . _CONF('UpdatePlayerControl_Url');
			$update_super_url = 'http://' . _CONF('gameServerIp') . ':' . $port . '/' . _CONF('UpdatePlayerSuperControl_Url');

			//存在超控
			if ($user['super_control'] == 1) {
				//超控概率
				foreach ($superControlList as $item) {
					if ($item['player_id'] == $user['usrid']) {
						if ($value > $item['start_val'] && $value < $item['end_val']) {
							$subVal = $item['control_val'];
							break;
						} else {
							if ($value == $item['start_val'] || $value == $item['end_val']) {
								$subVal = $item['control_val'];
								break;
							}
						}
					}

				}
				$updateData = [
					'sup_control_val' => $subVal,
				];
				// $this->Usr->update(['usrid'=>$user['usrid']],$updateData);
				$formData = [
					'playerid' => $user['usrid'],
					'sup_control_val' => $subVal,
					'super_control' => 1,
				];

				$response = post_api($update_super_url, $formData);
				//echo '更新用户超控'.$user['usrid']. 'updateUserSuperControl'.'-端口'.$port.'-'.$value.'  超控范围'.$subVal.PHP_EOL;

			}

			//没有超控就走单控
			if ($subVal == 0) {
				//最终概率
				if ($value > 0) {

					foreach ($lostList as $item) {
						if ($value > $item['start_val'] && $value < $item['end_val']) {
							$subVal = $item['control_val'];
							break;
						} else {
							if ($value == $item['start_val'] || $value == $item['end_val']) {
								$subVal = $item['control_val'];
								break;
							}

						}
					}

				} else {
					$value = -$value;
					// echo  '用户输钱'.PHP_EOL;
					foreach ($winList as $item) {
						if ($value > $item['start_val'] && $value < $item['end_val']) {
							$subVal = $item['control_val'];
							break;
						} else {
							if ($value == $item['start_val'] || $value == $item['end_val']) {
								$subVal = $item['control_val'];
								break;
							}
						}

					}
				}

				//更新单控并
				/*
				$updateData = [
					'control_val'=>$subVal,
				];
				$formData =[
					'playerid'=>$user['usrid'],
					'control_val'=>$subVal,
					'single_control'=>1,
				];
				$response =  post_api($update_single_url, $formData);
				*/
				//更新单控并更新超控
				$formDataSuper = [
					'playerid' => $user['usrid'],
					'sup_control_val' => 0,
					'super_control' => 1,
				];
				//  $response =  post_api($update_super_url, $formDataSuper);

				//echo '更新用户单控'.$user['usrid']. 'updateUserControl'.'-端口'.$port.'-'.$value.'  单控范围'.$subVal.PHP_EOL;

			}

			//xn_log('更新用户单控'.$user['usrid'], 'updateUserControl');
		}

	}


	// hook task_task_start.php
	public function playeroline($data)
	{

		$ymdhi = date('YmdHi');
		$agent_id = $this->User->select(['RoleID' => 9], [], 'id');
		$cond = ['agent_id' => arrlist_values($agent_id, 'id')];
		$cond['online'] = ['>' => 0];
		$cond['ai'] = 0;
		$users = $this->Usr->select($cond, [], 'usrid,online,chips,roomid,last_login_ip');
		$insert = [];
		$uid_arr = arrlist_values($users, 'usrid');

		foreach ($users as $k => $v) {
			$insert[] = [
				'ymdhi' => $ymdhi,
				'ip' => (!empty($v['last_login_ip']) && !is_null($v['last_login_ip'])) ? sprintf("%u", ip2long($v['last_login_ip'])) : 0,
				'gameid' => $v['roomid'],
				'uid' => $v['usrid'],
			];
		}
		$insert && $this->PlayerOline->insertALL($insert);
		$gamebilog = $this->Gamebilog->select(['Type' => [36, 50], 'DoAt' => 0], [], 'LogID,UsrID');
		if ($uid_arr) {
			foreach ($gamebilog as $v) {
				if (in_array($v['UsrID'], $uid_arr, 1)) {
					$this->Gamebilog->Doa($v['LogID']);
				}
			}
		}
	}




	public function settlement($data)
	{
		$time = time();
		if (empty($data['end'])) {
			$data['start'] = date('Y-m-d 00:00:00', strtotime("-1 day"));
			$data['end'] = date('Y-m-d 23:59:59', strtotime("-1 day"));
		} else {
			$start = $this->UserStat->find_one([], ['Ymd' => -1], 'Ymd');
			if (empty($start['Ymd'])) {
				$first = $this->MoneyTrans->find_one([], ['id' => 1]);
				$start['Ymd'] = date('Ymd', $first['times']);
			}
			$data['start'] = date('Y-m-d 00:00:00', strtotime($start['Ymd'] . '010000') + 86400);
		}

		$ymd = date('Ymd', strtotime($data['end']));
		$loac_k = 'stat_lock_' . $ymd;
		$lock = $this->UserStat->CacheGet($loac_k);
		if ($lock) {
			xn_log('生成' . date('Ymd', strtotime($data['start'])) . ' ~ ' . $ymd . '结算数据已锁定', 'settlement');
			return;
		}
		$this->UserStat->CacheSet($loac_k, $ymd, 3600);
		$agent = $this->User->select([], [], 'id,parent_id,share_rate,RoleID,username');
		$agent_id_value = arrlist_change_key($agent, 'id');
		$_ymd = $this->MoneyTrans->ymd_array($data['start'], $data['end']);

		$list = $this->UsrMoney->select(['ymd' => $_ymd]);

		$agent_array = [];
		foreach ($list as $arr) {//把游戏记录按照玩家分组
			$agent_id = $arr['agent_id'];
			$agent_array[$agent_id]['Game_Win'] = bcadd($agent_array[$agent_id]['Game_Win'], $arr['Game_Win']);
			$agent_array[$agent_id]['Poker_Pump'] = bcadd($agent_array[$agent_id]['Poker_Pump'], $arr['Poker_Pump']);
			$agent_array[$agent_id]['Reg_Ad'] = bcadd($agent_array[$agent_id]['Reg_Ad'], $arr['Reg_Ad']);
			$agent_array[$agent_id]['Active_Ad'] = bcadd($agent_array[$agent_id]['Active_Ad'], $arr['Active_Ad']);
			$agent_array[$agent_id]['Apply_Ad'] = bcadd($agent_array[$agent_id]['Apply_Ad'], $arr['Apply_Ad']);
			$agent_array[$agent_id]['Plat_Ad'] = bcadd($agent_array[$agent_id]['Plat_Ad'], $arr['Plat_Ad']);
		}

		$insert = 0;
		foreach ($agent as $_k => $_v) {
			if ($_v['RoleID'] != 9) {
				continue;
			}
			$k = $_v['id'];
			$v = isset($agent_array[$k]) ? $agent_array[$k] : ['Game_Win' => 0, 'Poker_Pump' => 0, 'Reg_Ad' => 0, 'Active_Ad' => 0, 'Apply_Ad' => 0, 'Plat_Ad' => 0];
			$js = $this->UserStat->read(['Ymd' => $ymd, 'AgentID' => $k]);
			if ($js['Ymd']) {
				continue;
			}
			$parent_id = $agent_id_value[$k]['parent_id'];//主号
			$zhid = $agent_id_value[$parent_id]['parent_id'];//管理号
			$glh = $agent_id_value[$zhid];//管理号
			$row = [
				'Ymd' => $ymd,
				'AgentID' => $k,
				'ParentID' => $agent_id_value[$k]['parent_id'],
				'Game_Win' => $v['Game_Win'],
				'Poker_Pump' => $v['Poker_Pump'],
				'Reg_Ad' => $v['Reg_Ad'],
				'Active_Ad' => $v['Active_Ad'] + $v['Plat_Ad'],
				'Apply_Ad' => $v['Apply_Ad'],
				'Rate' => $glh['share_rate'],
				'GLH' => $glh['id'],
				'CreateAT' => $time,
			];
			$this->UserStat->insert($row);
			$insert++;
		}
		empty($insert) AND $this->UserStat->insert(['Ymd' => $ymd, 'CreateAT' => $time]);
		xn_log('生成' . date('Ymd', strtotime($data['start'])) . '~' . $ymd . ' ' . implode(',', $_ymd) . '结算数据' . count($agent_array) . '条，推广号' . $insert . '个', 'settlement');
		$this->UserStat->CacheDel($loac_k);
		return true;
	}

	public function resetPlayerLoginFailNumber()
	{
		$day = date('Ymd');
		$key = 'resetplayerfail_lock_' . $day;
		$islock = $this->User->CacheGet($key);

		$where = ['errorNumber' => ['>' => 0], 'disable' => 1];
		$whereUsr['ai'] = 0;
		$whereUsr['disable'] = 1;
		$listCopy = $this->Account->select(['errorNumber' => ['>' => 0], 'disable' => 1], [], 'account');
		if (!$islock) {
			foreach ($listCopy as $item) {
				$this->Account->update(['account' => $item['account']], ['disable' => 0, 'errorNumber' => 0]);
				$this->Usr->update(['openid' => $item['account']], ['disable' => 0]);
				xn_log('重置用户登录错误次数' . date('Y-m-d H:i:s') . ' ' . $item['account'], 'resetplayerfail_log');
			}
		}
		$this->User->CacheSet($key, ['time' => $day], 86400);
	}

	public function updateIpRegion()
	{
		$where['ipregion'] = '0';
		$ipList = $this->Logintable->select($where, ['id' => -1], 'IP,ipregion,count(id) as playerCount', 1, 20, '', 'group by IP');
		$num = count($ipList);
		for ($i = 0; $i < $num; $i++) {
			$item = $ipList[$i];
			if (strlen($item['IP']) > 4) {
				$ip = trim($item['IP']);
				$apiUrl = _CONF('aliyungetIpAreaDetailUrl') . '/ip?ip=';
				$AppCode = _CONF('aliyungetIpAreaDetailAppCode');
				$headers = ['Authorization' => 'APPCODE ' . $AppCode];
				$response = http_get_api($apiUrl . $ip, $headers);

				$obj = json_decode($response);
				if ($obj->ret == 200) {
					$region = $obj->data->region;
					$ipArea = json_encode($obj->data);
					$this->Logintable->update(['IP' => $ip], ['ipregion' => $region, 'ipArea' => $ipArea]);
					xn_log('更新IP详情:' . $ip . ' ' . date('Y-m-d H:i:s') . '成功', 'upIpTxt');
				} else {
					if ($obj->ret == 40003) {
						$this->Logintable->update(['IP' => $ip], ['ipregion' => '内网', 'ipArea' => '']);
						xn_log('更新IP详情:' . $ip . ' ' . date('Y-m-d H:i:s') . '失败:内网地址', 'upIpTxt');
					} else {
						$this->Logintable->update(['IP' => $ip], ['ipregion' => '8', 'ipArea' => '']);

					}
				}
			} else {
				$this->Logintable->update(['id' => $item['id']], ['ipregion' => '9', 'IP' => '-1']);
			}
		}
	}


	/**
	 * 更新用户标识
	 */
	public function updateUserTodayWin()
	{
		$cond['online'] = ['>' => 0];
		$ulist = $this->Usr->select($cond, [], 'usrid,total_win,chips');
		xn_log('更新玩家标识:' . json_encode($ulist), 'updatePlayerFlag');
		$usrList = arrlist_key_values($ulist, 'usrid');
		$condTrans['UsrID'] = array_keys($usrList);
		$condTrans['Type'] = [51, 52, 61];
		$flagMap = [
			'1v1add' => 10,
			'1v2add' => 20,
			'1v3add' => 30,
			'1v1lower' => 1000,
			'1v2lower' => 2000,
			'1v3lower' => 3000,
		];
		if ($usrList) {
			$time = strtotime(date('Y-m-d'));
			$timeEnd = strtotime(date('Y-m-d') . ' 23:59:59');
			$condTrans['AddTime'] = ['>' => $time];
			$condTrans['AddTime'] = ['<' => $timeEnd];
			$list = $this->Gamebilog->select($condTrans, ['LogID' => -1], 'UsrID,Type,Money');
			$groups = arrlist_group($list, 'UsrID');
			foreach ($groups as $usrid => $group) {
				$uppoint = 0;
				$downpoint = 0;
				$win = 0;

				foreach ($group as $row) {
					if (in_array($row['Type'], [51, 61])) {//上分
						$uppoint += $row['Money'] * 0.95 / 1000;
						$win += $row['Money'] * 0.95 / 1000;;

					}
					if (in_array($row['Type'], [52]))//下分
					{
						$downpoint += -$row['Money'] / 1000;
						$win += -($row['Money'] / 1000);
					}
				}

				$addSubPoint = $uppoint - $downpoint;

				$flag = '-1';
				$chips = 0;
				foreach ($ulist as $userItem) {
					if ($userItem['usrid'] == $usrid) {
						$chips = $userItem['chips'];

					}
				}

				$chips = $chips / 1000;
				$addSubPoint -= $chips;
				if ($win < 2000) {
					if ($addSubPoint > 800) {
						$flag = $flagMap['1v1add'];
					} else if ($addSubPoint <= -800) {
						$flag = $flagMap['1v1lower'];
					}
				} else if ($win < 30000) {
					if ($addSubPoint > 2000) {
						$flag = $flagMap['1v2add'];
					} else if ($addSubPoint <= -2000) {
						$flag = $flagMap['1v2lower'];
					}
				} else {
					if ($addSubPoint > 5000) {
						$flag = $flagMap['1v3add'];
					} else if ($addSubPoint <= -5000) {
						$flag = $flagMap['1v3lower'];
					}
				}
				$this->Usr->update(['usrid' => $usrid], ['user_flag' => $flag]);
				xn_log('更新玩家标识:' . $usrid . ' ' . date('Y-m-d H:i:s') . ' 为 ' . $flag . ' $addSubPoint ' . $addSubPoint . '$win ' . $win . ' ' . ' $uppoint ' . ' ' . $uppoint . ' $downpoint ' . $downpoint . ' chips ' . $chips, 'updatePlayerFlag');

			}
//
//
//            foreach ($usrList as $userItem) //把游戏记录按照玩家分组
//            {
//
//                foreach ($list as $item) {
//                    if($item['usrid'] == $userItem['usrid'] )
//                    {
//                        if (in_array($item['style'], ['51', '61'])) {//上分
//                            $uppoint += $item['chips'] * 0.95 / 1000;
//
//                        }
//                        if (in_array($item['style'], ['52']))//下分
//                        {
//                            $downpoint += -$item['chips'] * 0.95 / 1000;
//
//                        }
//                    }
//
//                }
//
//                $addSubPoint = $uppoint - $downpoint;
//                $flag = '0';
//                $win = $userItem['total_win'] / 1000;
//
//                if ($win < 2000) {
//                    if ($addSubPoint > 1000) {
//                        $flag = '1v1add';
//                    } else if ($addSubPoint <= -500) {
//                        $flag = '1v1lower';
//                    }
//                } else if ($win < 40000) {
//                    if ($addSubPoint > 3000) {
//                        $flag = '1v2add';
//                    } else if ($addSubPoint <= -3000) {
//                        $flag = '1v2lower';
//                    }
//                } else {
//                    if ($addSubPoint > 5000) {
//                        $flag = '1v3add';
//                    } else if ($addSubPoint <= -5000) {
//                        $flag = '1v3lower';
//                    }
//                }
//                //$this->Usr->update(['usrid'=>$item['usrid']],['user_flag'=>$flag]);
//                xn_log('更新玩家标识:' . $item['usrid'] . ' ' . date('Y-m-d H:i:s') . ' 为 ' . $flag . ' $addSubPoint ' . $addSubPoint . '$win ' . $win . ' ' . ' $uppoint ' . ' ' . $uppoint . ' $downpoint ' . $downpoint, 'updatePlayerFlag');
//
//            }

		}

	}

	/**
	 * 注册直接送
	 */
	public function regFirstSong()
	{
		$openRegSong = false;
		if ($openRegSong) {
			$cond['online'] = ['>' => 0];
			$cond['reg_give'] = 0;
			$cond['ai'] = 0;
			$users = $this->Usr->select($cond, [], 'usrid,online,chips,roomid,last_login_ip');
			$money = 6000;
			foreach ($users as $k => $v) {
				$this->Gamebilog->Add($money, 51, $v['usrid'], $v['agent_id'], '127.0.0.1', time(), 0);
			}
		}

	}

	/**
	 * 商城上分失败重新回调上分
	 */
	public function updateShopFailSocoreOrders()
	{
		$time = time();
		$list = $this->Gamebilog->select(['Status' => 0]);
		$max = 86400 * 30;
		foreach ($list as $row) {
			if ($row['Type'] == 36 || $row['Type'] == 50) {
				if ($time - $row['AddTime'] > $max) {
					$this->Gamebilog->update(['LogID' => $row['LogID']], ['Status' => 2]);
				}
				continue;
			}
			if($time - $row['AddTime']>3){
				$this->Gamebilog->Doa($row['LogID']);
			}
		}
	}

	/**
	 * 更新同IP登录和注册的IP名单
	 */
	public function updateIpNameList()
	{
		$LatestLoginIPKey = 'LatestLoginIPKey_';
		$LatestRegIPKey = 'LatestRegIPKey_';
		$LatestLoginIP = $this->Usr->select(['ai'=>0], [], 'last_login_ip,count(1) as counts', 0, 0, '', 'group by last_login_ip having counts>1');
		$this->UserStat->CacheSet($LatestLoginIPKey, $LatestLoginIP, 60);
		$LatestRegIP = $this->Usr->select(['ai'=>0], [], 'regip,count(1) as counts', 0, 0, '', 'group by regip having counts>1');
		$this->UserStat->CacheSet($LatestRegIPKey, $LatestRegIP, 60);
		xn_log('更新同IP登录和注册的IP名单', 'updateIpNameList');
	}


	/**
	 * 更新同名提现列表
	 */
	public function updateSameNameCashList()
	{
		$LatestSameKey = 'SameNameCash_';
		$LatestSameList = $this->CashLog->select([], [], 'BankUser,count(1) as counts,UsrID', 0, 0, '', 'group by UsrID,BankUser having counts>1');
		$this->UserStat->CacheSet($LatestSameKey, $LatestSameList, 60);
		xn_log('更新同名提现', 'updateSameNameCashList');
	}


	/**
	 * 更新在线玩家输赢信息
	 */
	public function updateOnlineUserLastRecharge()
	{

		$cond['online'] = ['>' => 0];
		$cond['ai'] = 0;
		$users = $this->Usr->select($cond, [], 'usrid,online,chips,roomid,last_login_ip');
		$uid_arr = arrlist_values($users, 'usrid');

		$logWhere['UsrID'] = $uid_arr;
		$OnlineUserMoneyInfoKey = 'OnlineUserMoneyInfo_';
		$OnlineUserMoneyInfo = $this->UserStat->CacheGet($OnlineUserMoneyInfoKey);
		$OnlineUserMoneyInfo = [];
		//  select($cond = [], $order = [], $select = '*', $page = 0, $limit = 0, $key = '', $group = '')
		$logWhere['Type'] = ['51', '61'];
		$lastLogs = $this->Gamebilog->select($logWhere, ['LogID' => -1], 'max(LogID) lid ', 1, 10000, 'LogID', 'group by UsrID');

		$gid_arr = arrlist_values($lastLogs, 'lid');
		$gamebinlogsWhere['LogID'] = $gid_arr;
		$gamebinlogs = $this->Gamebilog->select($gamebinlogsWhere, ['LogID' => -1], ' LogID, UsrID,AfterMoney', 1, 10000, 'LogID', 'group by UsrID');
		foreach ($gamebinlogs as $item) {
			$OnlineUserMoneyInfo['_' . $item['UsrID']]['mn'] = $item['AfterMoney'];
			foreach ($users as $uItem) {
				if ($uItem['usrid'] == $item['UsrID']) {
					$OnlineUserMoneyInfo['_' . $item['UsrID']]['chips'] = $uItem['chips'];
					$OnlineUserMoneyInfo['_' . $item['UsrID']]['val'] = $uItem['chips'] / $item['AfterMoney'];
				}
			}

		}

		$this->UserStat->CacheSet($OnlineUserMoneyInfoKey, $OnlineUserMoneyInfo, 700);
		xn_log('更新玩家上次充值信息', 'updateOnlineUserLastRecharge');

	}


	// hook task_task_end.php
}
