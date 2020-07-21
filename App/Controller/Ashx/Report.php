<?php
/**
 * Created by PhpStorm.
 * User: yinhanmin
 * Date: 2019-03-09
 * Time: 08:49
 */

namespace Ashx;


use Ctrl\GameController;

class Report extends GameController
{


    public function report_report_Setscore()
    {

        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');
        $user = $this->request->param('user', '');
        $pageIndex = $this->request->param('pageIndex', 1);
        $pageSize = $this->request->param('pageSize', 20);
        if (empty($sDate)) {
            $sDate = date('Y-m-d 00:00:00');
            $eDate = date('Y-m-d H:i:s');
        }
        $user = $this->User->read_by_username($user);
        $this->hspower($user['id']);
        $cond = ['style' => [25, 29, 35, 36, 38,  50, 51, 52, 61, 114],  'times' => ['>=' => $sDate, '<=' => $eDate],'action_id' => $user['id']];
        $data['total'] = $this->MoneyTrans->count($cond);
        $data['totalAdd'] = 0;//总加分
        $data['totalReduce'] = 0;//总减分
        $data['totalSum'] = 0;//总累计
        $data['results'] = [];
        $list = $this->MoneyTrans->select($cond, ['id' => -1], '*', $pageIndex, $pageSize);
        $AdminUID = $this->agent_id_name;
        $usrid = arrlist_values($list, 'usrid');
        $usrs = $this->Usr->select(['usrid' => $usrid], [], 'usrid,openid');
        $usrs = arrlist_key_values($usrs, 'usrid', 'openid');
        $data['TotalAdd'] = calculate($this->MoneyTrans->sum('chips', [ 'style' => [25, 29, 35, 36, 50, 51, 61], 'times' => ['>=' => $sDate, '<=' => $eDate],'action_id' => $user['id']]));
        $data['TotalReduce'] = calculate($this->MoneyTrans->sum('chips', [ 'style' => [ 52, 114], 'times' => ['>=' => $sDate, '<=' => $eDate],'action_id' => $user['id']]));

        foreach ($list as $k => $v) {
            $vv['Rownum'] = $k + 1;
            $vv['Index_NO'] = $v['id'];
            $vv['Account'] = $AdminUID[$v['action_id']];
            $vv['Action_Type'] = $v['style'];
            $vv['UserName'] = $usrs[$v['usrid']] . '(' . $v['usrid'] . ')';
            $vv['ScoreNum'] = calculate($v['chips']);
            $vv['AfterNum'] = calculate($v['EndBlance']);
            $vv['C_DateTime'] = $v['times'];
            $vv['ActionIP'] = $v['action_ip'];
            $data['results'][] = $vv;
        }
        $this->response('0000', $data);
    }

    public function report_report_SetscoreDetailSub()
    {

        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');
        $user = $this->request->param('user', '');

        $admin = $this->User->read_by_username($user);
        $this->hspower($admin['id']);

        $start = $sDate . ' 00:00:00';
        $end = $eDate . ' 23:59:59';

        $cond = [];
		$cond['style'] = array_keys($this->MoneyTrans->Type);
        $cond['times'] = ['>=' => $start, '<=' => $end];
		$cond['action_id'] = $admin['id'];

        $list = $this->MoneyTrans->select($cond, [],'usrid,style,chips,times,BeginBlance,EndBlance,gameID');
		$list = arrlist_multisort($list,'times',false);
        $UsrID = arrlist_values($list, 'usrid');
        if ($this->isadmin) {
            $users = $this->Usr->select(['usrid' => $UsrID], [], 'usrid,openid');
            $users = arrlist_change_key($users, 'usrid');
        }
        $data = ['results'=>[]];
        $Type = array_keys($this->MoneyTrans->Type);
        foreach ($list as $v) {
        	if($v['style']==38){
				continue;
			}
            $row = [
                "total_plat" => 0.00,
                "TotalPay" => 0.00,
                "total_reg" => 0.00,
                "Total_Cash_Rengong" => 0.00,
                "TotalReduceMoney_SubUser" => 0.00,
                "total_apply" => 0.00,
                "PlayerName" => '',
                "TotalTransfer_Success" => 0.00,
                "total_active" => 0.00,
                "TotalAddMoney_SubUser" => 0.00,
                "Total_Cash_Cancel" => 0.00
            ];
            $username = $v['UsrID'];
            if ($this->isadmin) {
                $username = $users[$v['usrid']]['openid'] . '(' . $v['usrid'] . ')';
            }
            $row['PlayerName'] = $username;
            if (!in_array($v['style'], $Type) || $v['style'] == 39 || $v['style'] == 114) {
                continue;
            }
			$chips =$v['EndBlance']- $v['BeginBlance'];

            switch ($v['style']) {
                case 51:
                    $row['TotalPay'] = calculate($chips);
                    break;
                case 61:
                    $row['TotalPay'] = calculate($chips);
                    break;
                case 52:
                    $row['Total_Cash_Rengong'] = calculate(-$chips);
                    break;
                case 25:
                    $row['total_reg'] = calculate($chips);
                    break;
                case 29:
                    $row['total_apply'] = calculate($chips);
                    break;

                case 1://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                case 2://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                case 3://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                case 4://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                case 5://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                case 6://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                case 8://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                case 10://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                case 17://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                case 35:
                    $row['total_plat'] = calculate($chips);
                    break;
                case 9://1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励
                    $row['diamond']==0 && $row['total_plat'] = calculate($chips);
                    break;
                case 36:
                case 50:
                    $row['total_active'] = calculate($chips);
                    break;
//                case 114:
//                    $row['Total_Cash_Rengong'] = -calculate($chips);
//                    break;
                case 37:
                    $row['Total_Cash_Rengong'] = calculate($chips);
                    break;
                case 38:

                    $row['Total_Cash_Rengong'] = -calculate($chips);
                    break;
            }

			//$chips!=$v['chips'] && $row['PlayerName'].=' '.$v['chips'];
            $data['results'][] = $row;

        }

        $this->response('0000', $data);
    }

    public function report_AccountReport_Total()
    {
        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');
        $Type = $this->request->param('Type', '');
        $user = $this->request->param('user', '');
        $rbl = $this->request->param('rbl', '');
        $rbl = $rbl ? 0 : 1;
        $data['results'] = [];
        $data['ViewTYPE'] = $rbl;
        if ($Type == 'AgentTotalReport') {
        	$agent = $this->User->read_by_username($user);
            $this->hspower($agent['id']);
            $start = strtotime($sDate . ' 00:00:00');
            $end = strtotime($eDate . ' 23:59:59');
            $list = $this->User->select(['parent_id' => $agent['id']]);
            foreach ($list as $v) {
                $data['results'][] = $this->stat_agent_start_end($v, $start, $end);
            }
            //代理上下总帐
        } elseif ($Type == 'ServerTotalReport') {

            $agent = $this->User->read_by_username($user);
            $this->hspower($agent['id']);
            $start = strtotime($sDate . ' 00:00:00');
            $end = strtotime($eDate . ' 23:59:59');
            $list = $this->Usr->select(['agent_id' => $agent['id']]);
            foreach ($list as $v) {
                $data['results'][] = $this->stat_player_start_end($v, $start, $end,$this->roleInfo);
            }

        }
        $this->response('0000', $data);
    }

    public function report_AccountReport()
    {
        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');
        $Type = $this->request->param('UTYPE', '');
        $rbl = $this->request->param('rbl', 0);
        $user = $this->request->param('user', '');

        $data['ViewTYPE'] = $rbl ? 0 : 1;
        if ($Type == 'AgentReport') {
            $agent = $this->User->read_by_username($user);
            $this->hspower($agent['id']);
            $start = $sDate . ' 00:00:00';
            $end = $eDate . ' 23:59:59';
            $data['results'] = $this->MoneyTrans->stat_game_date_new($start, $end,$agent['id']);

        } elseif ($Type == 'ServerReport') {
            $user = $this->Account->read_one(['account' => $user]);
			$this->hspower($user['usrid'],1);
            $start = $sDate . ' 00:00:00';
            $end = $eDate . ' 23:59:59';
            $data['results'] = $this->MoneyTrans->stat_game_date_new( $start, $end,$user['usrid'],1);
        }
        $this->response('0000', $data);
    }

    public function report_report_setscoreindex()
    {

        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');

        $agent = $this->User->read_by_username($this->default_user['username']);
        $this->hspower($agent['id']);

        if (empty($sDate)) {
            $sDate = date('Y-m-d 00:00:00');
            $eDate = date('Y-m-d H:i:s');
        } else {
            $sDate = $sDate . ' 00:00:00';
            $eDate = $eDate . ' 23:59:59';
        }
        $list = $this->User->select(['RoleID' => [1,2,3,101, 102, 103, 104, 105]], ['id' => -1], 'id,username,note');
		$list[]=['note'=>'','username'=>'商城上分','id'=>0];
        foreach ($list as $k => $v) {
        	$row = $this->MoneyTrans->stat_sum_agent($sDate,$eDate,$v['id']);
            $data['results'][] = [
                "Rownum" => $k+1,
                "Redme" => $v['note'],
                "Account" => $v['username'],
                "sgcz" => $row['Sg_Ad'],//手工充值
                "sccz" =>$row['Sc_Ad'],//商城充值
                "rgdh" => -$row['Sg_Sub'],
                "sgdh" => -$row['Cash_Ad']-$row['Cash_C_Ad'],
                "total_reg" => $row['Reg_Ad'],
                "total_plat" => $row['Plat_Ad'],
                "total_apply" => $row['Apply_Ad'],
                "total_active" => $row['Active_Ad'],
                "total_agent" => $row['Agent_Ad']
            ];
        }

        $this->response('0000', $data);
    }

    public function report_report_TotalSetscore()
    {

        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');
        $Type = $this->request->param('Type', '');
        $user = $this->request->param('user', '');
        $rbl = $this->request->param('rbl', '');
        $rbl = $rbl ? 0 : 1;
        $agent = $this->User->read_by_username($user);
        $this->hspower($agent['id']);
        $user_list = $this->User->select(['parent_id' => $agent['id']]);
        $data['results'] = [];
        $data['ViewTYPE'] = $rbl;
        $sDate = $sDate . ' 00:00:00';
        $eDate = $eDate . ' 23:59:59';
        foreach ($user_list as $v) {
        	$row = $this->MoneyTrans->stat_sum_new($sDate,$eDate,$v['id']);

            $data['results'][] = [
                "Account" => $v['username'],
                "sgcz" => $row['Sg_Ad'],//手工充值
                "sccz" =>$row['Sc_Ad'],//商城充值
                "rgdh" => -$row['Sg_Sub'],
                "sgdh" => -$row['Cash_Ad']-$row['Cash_C_Ad'],
                "total_reg" => $row['Reg_Ad'],
                "total_plat" => $row['Plat_Ad'],
                "total_apply" => $row['Apply_Ad'],
                "total_active" => $row['Active_Ad'],
                "total_agent" => $row['Agent_Ad']
            ];

        }

        $this->response('0000', $data);
    }

    public function Plat_gm_data()
    {
        $ids = $this->agent_child_code($this->default_user["id"]);
        $ids[] = $this->default_user["id"];
        $key = 'money_total_' . md5(xn_json_encode($ids));
        $num = $this->Usr->CacheGet($key);
        if (!$num) {
            $num = $this->Usr->sum('chips+bankchips', ['agent_id' => $ids]);
            $this->Usr->CacheSet($key, $num, 60);
        }
        $this->response("0000", ["data" => ["money_total_normal" => calculate($num)]]);

    }
}