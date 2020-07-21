<?php
/**
 * Created by PhpStorm.
 * User: yinhanmin
 * Date: 2019-03-09
 * Time: 08:48
 */

namespace Ashx;


use Ctrl\GameController;


class Log extends GameController
{


    public function log_rank_virtual()
    {
        $data['results'] = $this->PaymentCustom->select([], ['ID' => -1], 'ID,Status,RankNo,NickName,head_img,MoneyNum,Signature,WeChatID,UserName');
        $this->response('0000', $data);
    }

    public function log_custom()
    {
        $data['results'] = $this->Custom->select([], ['ID' => -1]);
        $this->response('0000', $data);
    }

    public function log_room()
    {
        $this->needadmin(1);
        $data = $this->RoomBank->select();
        $totalBank = 0;//总奖池
        $totalBrokerage = 0;//总佣金
        $totalJackport = 0;//总佣金
        $return['success'] = true;
        $return['results'] = $data;
        $num = count($data);
        for ($i = 0; $i < $num; $i++) {
            $totalBank += $data[$i]['bank'];
            $totalBrokerage += $data[$i]['brokerage'];
            $totalJackport += $data[$i]['jackport'];
        }
        $return['bank'] = $totalBank;
        $return['brokerage'] = $totalBrokerage;
        $return['jackport'] = $totalJackport;
        $this->response('0000', $return);
    }

    public function log_GetCashNoticeLog()
    {
        $data['TotalNum'] = $this->CashLog->count(['Status' => 10]);
        $this->response('0000', $data);
    }

//    public function log_GetApplyNotice()
//    {
//        $data['results'] = $this->SysNotice->select(['IsEnable' => 1]);
//        $this->response('0000', $data);
//    }

    public function log_GetSettlementLog()
    {
        $data['TotalNum'] = $this->UserSettlement->count(['Status' => 1]);
        $this->response('0000', $data);
    }
    public function log_GetApplyNoticeLog()
    {
        $data['TotalNum'] = $this->ApplyLog->count(['Status' => 0]);
        $this->response('0000', $data);
    }

    public function log_GetRegLog()
    {

        $cond['agent_id'] = $this->default_user['id'];
        $cond['regtime'] = ['>=' => date('Y-m-d 00:00:00'), '<=' => date('Y-m-d H:i:s')];
        $cond['reg_give'] = 0;
        $data['TotalNum'] = $this->Usr->count($cond);
        $this->response('0000', $data);
    }


    public function log_getMoveUserLog()
    {

        $list = $this->UserMove->select([], ['ID' => -1], 'ID,Account,UserName,AfterAgent,BeforeAgent,C_DateTime', 1, 100);
        $data['results'] = $list;
        $this->response('0000', $data);
    }

    public function log_QueryGetDelUserInfo()
    {
        $data['results'] = [];
        $this->response('0000', $data);
    }


    public function log_UnlockUserLog()
    {

        $data = [];
        $user = $this->request->param("user", "");
        if (strlen($user) == 6 || !is_numeric($user)) {
            $agent = $this->User->read_by_username($user);
        } else {
            $agent = $this->User->read_by_id($user);
        }
        $this->hspower($agent['id']);
        $list = $this->UserLockLog->select(['UserID' => $agent['id'], 'Type' => 2]);
        $users = $this->agent_id_name;
        $data['results'] = [];
        foreach ($list as $v) {
            $row = [
                'C_DateTime' => date('Y-m-d H:i:s', $v['CreateAT']),
                'Account' => $users[$v['AdminID']],
                'UserName' => $users[$v['UserID']],

            ];
            $data['results'][] = $row;
        }
        $this->response('0000', $data);
    }

    public function log_UnlockUser()
    {
        $this->needadmin(1);
        $user = $this->request->param('user', '');

        if (strlen($user) == 6 || !is_numeric($user)) {
            $agent = $this->User->read_by_username($user);
        } else {
            $agent = $this->User->read_by_id($user);
        }

//        //if (is_numeric($user) && !isset($user{10})) {
//        //    $agent = $this->User->read_by_id($user);
//        //} else {
//            $agent = $this->User->read_by_username($user);
//        //}
        $this->hspower($agent['id']);
        $this->User->update(['id' => $agent['id']], ['islock' => 0, 'status' => 1, 'login_failed_count' => 0]);
        $this->UserLockLog->Add($agent['id'], $this->token['id'], $this->request->get_client_ip(1), 2);
        $this->OperateLog->Add($this->token['id'], $agent['id'], 1, $this->request->get_client_ip(1), '解锁账户[' . $agent['username'] . ']');
        $this->response('0000', [], '解锁完成');
    }


    public function log_UnlockNewPlayer()
    {
        $_user = $this->request->param('user', '');
        $user = trim(str_replace('-', '', $_user));
        $this->Account->update(['account' =>$user], ['disable' => 0,'errorNumber'=>0]);
        $desc =  '解锁新玩家账户 ' .$user ;
        $this->OperateLog->Add($this->token['id'], 0, 0, $this->request->get_client_ip(1), $desc);
        $this->response( '0000' );

    }

    public function log_ResetPlayerOnlineStatus()
    {
        $_user = $this->request->param('user', '');
        $user = trim(str_replace('-', '', $_user));
        $where['usrid'] = $user;
        if(strlen($user)==11)
        {
            $where['openid'] = $user;
        }
        $this->Usr->update($where, ['online' => 0,'roomid'=>0]);
        //$desc =  '玩家账户 ' .$user ;
        //$this->OperateLog->Add($this->token['id'], 0, 0, $this->request->get_client_ip(1), $desc);
        $this->response( '0000' );

    }

    public function log_AccountApplyog()
	{
		$pageIndex = $this->request->param('pageIndex', 1);
		$pageSize = $this->request->param('pageSize', 20);
		if ($this->isadmin) {
			$cond = [];
		} else {
			$cond['UsrID'] = $this->token['id'];
		}
		$list = $this->ApplyLog->GetList($cond,['LogID'=>-1],$pageIndex,$pageSize);
		$usrid = arrlist_values($list['results'],'UsrID');
		if ($usrid) {
			$users = $this->Usr->select(['usrid' => $usrid], [], 'usrid,openid');
			$users = arrlist_key_values($users, 'usrid', 'openid');
		}
		$agents = $this->agent_id_name;
		$data['results'] = [];
		$start = ($pageIndex - 1) * $pageSize;
		foreach ($list['results'] as $k => $v) {
			$v['Rownum'] = $start + $k + 1;
			$v['UserName'] = isset($users[$v['UsrID']]) ? $users[$v['UsrID']] : '-';
			$v['AgentName'] = isset($agents[$v['AgentID']]) ? $agents[$v['AgentID']] : '-';
			$v['CreateAT'] = date('Y-m-d H:i:s', $v['AddTime']);
			$v['Money'] = calculate($v['Money']);
			$v['ApplyMoney'] = calculate($v['ApplyMoney']);
			$v['IP'] = long2ip($v['IP']);
			$v['Status_fmt']=$this->ApplyLog->Status[$v['Status']];
			$data['results'][] = $v;
		}

		$data['total'] =$list['total'];//intval($this->ApplyLog->count($cond));
		$this->response('0000',$data);
	}

    public function log_AccountOperateLog()
    {

        $pageIndex = $this->request->param('pageIndex', 1);
        $pageSize = $this->request->param('pageSize', 20);

        if ($this->token['RoleID'] == 1 || $this->token['RoleID'] == 2) {
			$user = $this->request->param('user', 0);
			$sDate = $this->request->param('sDate', '');
			$eDate = $this->request->param('eDate', '');
			$info = $this->request->param('info', '');

			$cond = [];
			$user && $cond['UserID']=$user;
			$sDate && $cond['CreateAt']['>=']=strtotime($sDate.' 00:00:00');
			$eDate && $cond['CreateAt']['<=']=strtotime($eDate.' 23:59:59');
			$info && $cond['Desc']['LIKE']=$info;
        } else {
            $cond['UserID'] = $this->token['id'];
        }

        $list = $this->OperateLog->GetList($cond, ['ID' => -1], $pageIndex, $pageSize);
        $adminid = [];
        $usrid = [];
        $agents = [];
        $users = [];
        foreach ($list['results'] as $k => $v) {
            $adminid[] = $v['UserID'];
            $v['Type'] == 1 ? $adminid[] = $v['UsrID'] : $usrid[] = $v['UsrID'];
        }

        if ($adminid) {
            $agents = $this->agent_id_name;
        }
        if ($usrid) {
            $users = $this->Usr->select(['usrid' => $usrid], [], 'usrid,openid');
            $users = arrlist_key_values($users, 'usrid', 'openid');
        }

        $data['results'] = [];
        $start = ($pageIndex - 1) * $pageSize;
        foreach ($list['results'] as $k => $v) {
            $v['Rownum'] = $start + $k + 1;
            $v['Account'] = isset($agents[$v['UserID']]) ? $agents[$v['UserID']] : '-';

            if ($this->default_user['RoleID'] == 1 || $this->default_user['RoleID'] == 2) {
                $v['Type'] == 0 && $v['UserName'] = isset($users[$v['UsrID']]) ? $users[$v['UsrID']] : '-';
                $v['Type'] == 1 && $v['UserName'] = isset($agents[$v['UsrID']]) ? $agents[$v['UsrID']] : '-';

                if($this->token['RoleID']==101)
                {
                    $v['UserName'] = $v['UsrID']==0?'-':$v['UsrID'];
                }
            } elseif ($this->isadmin) {
                $v['Type'] == 0 && $v['UserName'] = !empty($v['UsrID']) ? $v['UsrID'] : '-';
                $v['Type'] == 1 && $v['UserName'] = isset($agents[$v['UsrID']]) ? 'A' . $agents[$v['UsrID']] : '-';
                $v['UserName'] = '**';
            } else {
                $v['UserName'] = !empty($v['UsrID']) ? $v['UsrID'] : '-';
                $v['UserName'] = '*';
            }

            $v['CreateAT'] = date('Y-m-d H:i:s', $v['CreateAt']);
            $v['IP'] = $this->isadmin? long2ip($v['IP']):'-';
            $data['results'][] = $v;
        }

        $data['total'] = intval($this->OperateLog->count($cond));
        $this->response('0000', $data);
    }

    public function log_Feedback()
    {

        $pageIndex = $this->request->param('pageIndex', 1);
        $pageSize = $this->request->param('pageSize', 20);


        $cond = [];
        $cond['touid'] =0;
        $data['results'] = [];
        $list = $this->FeedBack->GetList($cond, ['id' => -1], $pageIndex, $pageSize);
        $start = ($pageIndex - 1) * $pageSize;
        foreach ($list['results'] as $k => $v) {
            $v['Rownum'] = $start + $k + 1;
            $v['reply'] = '未回复';
            $v['question'] = string_remove_xss($v['question']);
            if(empty($v['reply'])||$v['reply']==null)
            {
                $v['reply'] = '未回复';
            }
            $data['results'][] = $v;
        }

        $data['total'] = intval($this->FeedBack->count($cond));
        $this->response('0000', $data);
    }


    public function log_SysFeedback()
    {

        $pageIndex = $this->request->param('pageIndex', 1);
        $pageSize = $this->request->param('pageSize', 20);

        $cond = [];
        $data['results'] = [];
        $list = $this->SyeFeedBack->GetList($cond, ['id' => -1], $pageIndex, $pageSize);
        $start = ($pageIndex - 1) * $pageSize;
        foreach ($list['results'] as $k => $v) {
            $v['Rownum'] = $start + $k + 1;
            $v['add_time'] = date('Y-m-d H:i:s', $v['add_time']);
            $v['content'] = string_remove_xss($v['content']);
            $data['results'][] = $v;
        }

        $data['total'] = intval($this->SyeFeedBack->count($cond));
        $this->response('0000', $data);
    }


    public function log_Last20ScoreLog()
    {
        $userNamec = $this->request->param('userc', '');
        if ($userNamec) {
            //$userName = xn_decrypt($userNamec, _CONF('salt'));
            $userName = $userNamec;
        } else {
            $userName = $this->request->param('user', '');

        }
        $user = $this->findPlayer($userName);
        $list = $this->MoneyTrans->select(['usrid' => $user['usrid'], 'style' => array_keys($this->MoneyTrans->Type),'diamond' =>0], ['times' => -1,'id'=>-1], '*', 1, 20);
        $AdminID = arrlist_values($list, 'action_id');
        if ($AdminID) {
            $AdminID = $this->agent_id_name;
        } else {
            $AdminID = [];
        }
        $data['results'] = [];
        foreach ($list as $k => $v) {
            $vv['Rownum'] = $k + 1;
            $vv['Index_NO'] = $v['id'];
            $vv['Account'] = !empty($AdminID[$v['action_id']]) ? $AdminID[$v['action_id']] : '-';
            $vv['Action_Type'] = intval($v['style']);

            if($this->roleInfo['dh']){
                $vv['UserName'] = $user['openid'];
            }else{
                $vv['UserName'] = $user['usrid'];
            }

            $vv['ScoreNum'] = calculateSub2($v['chips']);
            $vv['AfterNum'] = calculateSub2($v['EndBlance']);
            $vv['C_DateTime'] = $v['times'];
			//$vv['action_ip'] = $v['action_ip'];
            $vv['ActionIP'] = $v['action_ip'];
            $data['results'][] = $vv;
        }
        $this->response('0000', $data);
    }

    public function log_setUserscoreLog()
    {

        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');
        $user = $this->request->param('user', '');
        $pageIndex = $this->request->param('pageIndex', 1);
        $pageSize = $this->request->param('pageSize', 20);


        $user_info = $this->findPlayer($user);

        if (empty($sDate)) {
            $sDate = date('Y-m-d 00:00:00');
            $eDate = date('Y-m-d H:i:s');
        }
        //$this->MoneyTrans->Type;
        $cond = ['usrid' => $user_info['usrid'], 'style' => array_keys($this->MoneyTrans->Type),'diamond' =>0, 'times' => ['>=' => $sDate, '<=' => $eDate]];

        $data['totalAdd'] = 0;//总加分
        $data['totalReduce'] = 0;//总减分
        $data['totalSum'] = 0;//总累计
        $data['results'] = [];
        $list = $this->MoneyTrans->GetList($cond, ['id' => -1], $pageIndex, $pageSize);
        $AdminID = $this->agent_id_name;
        $data['total'] = $list['total'];
        $data['regtime']=$user_info['regtime'];
        $data['totalAdd'] = $this->MoneyTrans->sum('chips', ['usrid' => $user_info['usrid'], 'style' => [25, 29, 35, 36, 50, 51, 61, 71], 'times' => ['>=' => $sDate, '<=' => $eDate]]);
        $data['totalReduce'] = $this->MoneyTrans->sum('chips', ['usrid' => $user_info['usrid'], 'style' => [52, 37, 38,  114], 'times' => ['>=' => $sDate, '<=' => $eDate]]);
        $data['totalSum'] = calculate(bcadd($data['totalAdd'],$data['totalReduce']));
		$data['totalAdd'] = calculate($data['totalAdd']);
		$data['totalReduce'] = calculate($data['totalReduce']);

		$data['totalSys'] = calculate($this->MoneyTrans->sum('chips', ['usrid' => $user_info['usrid'], 'style' => [1,2,3,4,5,6,7,8,9,10,17,35], 'diamond' =>0,'times' => ['>=' => $sDate, '<=' => $eDate]]));
        $data['totalreg'] = calculate($this->MoneyTrans->sum('chips', ['usrid' => $user_info['usrid'], 'style' => 25, 'times' => ['>=' => $sDate, '<=' => $eDate]]));
		$data['totalActive'] = calculate($this->MoneyTrans->sum('chips', ['usrid' => $user_info['usrid'], 'style' => [36,50], 'times' => ['>=' => $sDate, '<=' => $eDate]]));
		$data['totalApply'] = calculate($this->MoneyTrans->sum('chips', ['usrid' => $user_info['usrid'], 'style' => 29, 'times' => ['>=' => $sDate, '<=' => $eDate]]));

		foreach ($list['results'] as $k => $v) {
            $vv['Rownum'] = $k + 1;
            $vv['Index_NO'] = $v['id'];
            $vv['Account'] = !empty($AdminID[$v['action_id']]) ? $AdminID[$v['action_id']] : '-';
            $vv['Action_Type'] = $v['style'];
            if ($this->isadmin) {
                $vv['UserName'] = $user_info['openid'] . '(' . $v['usrid'] . ')';
            } else {
                $vv['UserName'] = $v['usrid'];
            }

            $vv['ScoreNum'] = calculate($v['chips']);
            $vv['AfterNum'] = calculate($v['EndBlance']);
            $vv['C_DateTime'] = $v['times'];
            $vv['ActionIP'] = $v['action_ip'] ? $v['action_ip'] : '系统';
            $data['results'][] = $vv;
        }
        $this->response('0000', $data);
    }


    public function Log_playerBetLog()
    {

        $sGameID = $this->request->param('sGameID', '');
        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');
        $user = $this->request->param('user', '');

        $pageIndex = $this->request->param('pageIndex', 1);
        $pageSize = $this->request->param('pageSize', 20);
        $user = $this->findPlayer($user);
        if (empty($sDate)) {
            $end = time();
            $end_day = date('Y-m-d 23:59:59', $end);
            $week_last = strtotime("$end_day Sunday");
            $week_last_day = date('Y-m-d', $week_last);
            $week_first = strtotime("$week_last_day -6 days");
            $start = $week_first;
        } else {
            $start = strtotime($sDate);
            $end = strtotime($eDate);
            if ($end > time()) $end = time();
        };

        $data['ViewTYPE'] = 1;
        $data['results'] = [];
        $cond = [];
        $cond['usrid'] = $user['usrid'];
        $sGameID > 0 && $cond['gameID'] = $sGameID;
        $cond['times'] = ['>=' => date('Y-m-d H:i:s', $start), '<=' => date('Y-m-d H:i:s', $end)];
        $this->MoneyTrans->count($cond) > 200000 AND $this->response('0001', [], '数据量太大,请选择分段查询');
        $data = $this->MoneyTrans->GameList($cond, $pageIndex, $pageSize);
        $this->response('0000', $data);
    }

    public function Log_playerBetLog2()
    {

        $sGameID = $this->request->param('sGameID', '');
        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');
        $user = $this->request->param('user', '');

        $pageIndex = $this->request->param('pageIndex', 1);
        $pageSize = $this->request->param('pageSize', 20);
        $user = $user ? $this->findPlayer($user) : [];
        if (empty($sDate)) {
            $end = time();
            $end_day = date('Y-m-d 23:59:59', $end);
            $week_last = strtotime("$end_day Sunday");
            $week_last_day = date('Y-m-d', $week_last);
            $week_first = strtotime("$week_last_day -6 days");
            $start = $week_first;
        } else {
            $start = strtotime($sDate);
            $end = strtotime($eDate);
            if ($end > time()) $end = time();
        };
        $data['ViewTYPE'] = 1;
        $data['results'] = [];
        $cond = [];
        $user['usrid'] && $cond['usrid'] = $user['usrid'];
        if ($sGameID) {
            $cond['gameID'] = $sGameID;
        } else {
            //$cond['gameID'] = $this->Game->fengkong;
            $fk = $this->request->param('fk', '0');
            if($fk==1)
            {


                //$fkGameList = $this->RoomBank->select(['fk'=>1], [], $select = '*');
                $where['fk'] = 1;
                // $fkGameList = $this->RoomBank->select([], [],  '*');
                $fkGameList = $this->RoomBank->select(['fk'=>1]);

                $roomIds = [];
                foreach ($fkGameList as $item)
                {
                    $roomIds[] = $item['roomID'];
                }

                if(count($roomIds)>0)
                {
                    $cond['gameID'] =  $roomIds;
                }
            }
        }
        
      // $cond['gameID'] =  10001;
        $cond['times'] = ['>=' => date('Y-m-d H:i:s', $start), '<=' => date('Y-m-d H:i:s', $end)];
        //$cond['style'] = 0;
        $cond['gameNumb'] = ['>' => 0];
        //return json_encode($cond);
        //return json_encode($list);
        $this->MoneyTrans->count($cond) > 100000 AND $this->response('0001', [], '数据量太大,请选择分段查询');
        $data = $this->MoneyTrans->GameList2($cond, $pageIndex, $pageSize);
        $this->response('0000', $data);
    }

    public function Log_historysettle()
    {
        $userNamec = $this->request->param('user', '');
        if ($userNamec) {
            //$userName = xn_decrypt($userNamec, _CONF('salt'));
            $userName = $userNamec;
        } else {
            $userName = $this->request->param('user', '');

        }
        $user = $this->User->read_by_username($userName);

        $where['AgentID'] = $user['id'];
       // $where['is_delete'] = 0;
        $list = $this->UserSettlement->select($where, ['Ymd'=>-1], '*', 1, 30);
        $data['results'] = [];
        $start = 0;
        foreach ($list as $k => $v) {
              $start++;
              $v['Rownum'] = $start;
              $v['Sub'] = calculate($v['Sub']);
              $v['Add'] = calculate($v['Add']);
              $v['Num'] = calculate($v['Num']);
              $v['RealNum'] = calculate($v['RealNum']);
              $v['CreateAT'] = date('Y-m-d', $v['CreateAT']);
              $data['results'][] = $v;
        }
        $data['id']=$user['id'];
        $this->response('0000', $data);

    }



}