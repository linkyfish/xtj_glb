<?php

namespace Ctrl;

// hook admincontroller_use.php


Class GameController extends AdminController
{

    public $agent_array = [];
    public $agent_son = [];
    public $agent_key_value = [];
    public $agent_first = [];
    public $default_user = [];
    public $agent_id_name = [];
    public $agent_id_guid = [];
    public $roleInfo = [];

    public $rolekv = [];
    public $nav = [];

    public $isadmin = 0;
    public $isAgentUser = 0;

    public function __construct($server, $route, \Request $request, $response)
    {
        parent::__construct($server, $route, $request, $response);



        $nav = $this->NewMenu->CacheGet('AdminNav');
        if (empty($nav)) {
            $nav = $this->NewMenu->select(['Status' => 1]);
            $this->NewMenu->CacheSet('AdminNav', $nav, 600);
        }


        $nav = arrlist_multisort($nav, 'Rank');
        //准备递归用的数据
        $this->agent_array = $this->User->CacheGet('Agent_Array');
        if (empty($this->agent_array) || is_null($this->agent_array)) {
            $this->agent_array = $this->User->select(['is_deleted' => 0], '', 'id,parent_id,balance,RoleID,username,guid');
            $this->User->CacheSet('Agent_Array', $this->agent_array, 600);
        }

        $this->agent_son = $this->User->CacheGet('Agent_Son');
        if (empty($this->agent_son) || is_null($this->agent_son)) {
            $agent_son = [];
            foreach ($this->agent_array as $v) {
                !isset($agent_son[$v['id']]) && $agent_son[$v['id']] = [];
                !isset($agent_son[$v['parent_id']]) && $agent_son[$v['parent_id']] = [];
                $agent_son[$v['parent_id']][] = ['id' => $v['id'], 'RoleID' => $v['RoleID'], 'balance' => $v['balance']];
            }
            $this->agent_son = $agent_son;
            $this->User->CacheSet('Agent_Son', $this->agent_son, 600);
        }


        $this->agent_key_value = arrlist_key_values($this->agent_array, 'id', 'parent_id');
        $this->agent_id_name = arrlist_key_values($this->agent_array, 'id', 'username');
        $this->agent_id_guid = arrlist_key_values($this->agent_array, 'id', 'guid');


        $agent_first = $this->User->CacheGet('is_root_agent');
        if (empty($agent_first) || is_null($agent_first)) {
            $agent_first = $this->User->read(['is_root_agent' => 1]);
            $this->User->CacheSet('is_root_agent', $agent_first, 600);
        }

        $this->default_user = $this->token;
        $this->isadmin = 0;
        $col_1 = 0;
        $col_2 = 0;

        if (in_array($this->token["RoleID"], $this->admin_role_arr)) {
            $this->default_user = $agent_first;
            $this->isadmin = 1;
            $col_1 = 1;
            $col_2 = 0;
        }

        $this->roleInfo = $this->rolearray[$this->token['RoleID']];
        if ($this->token['RoleID'] != 1) {
            $roleInfoArr = explode(",", $this->roleInfo["PowerList"]);
            $_nav = $nav;
            $nav = [];
            foreach ($_nav as $k => $v) {
                in_array($v['Node'], $roleInfoArr, 1) AND $nav[] = $v;
            }
        }
        if($this->token['RoleID']==11 || $this->token['RoleID']==10|| $this->token['RoleID']==9){
           $son = $this->agent_son[$this->token['id']];
           $arr=[];
           foreach ($son as $v){
               $v['RoleID']==10 && $arr[]=$v['id'];
           }
            $child_list=$arr;
            foreach ($arr as $id_s) {
                is_array($this->agent_son[$id_s]) && $child_list = array_merge($child_list, $this->agent_son[$id_s]);
            }
            $this->roleInfo['child_list']=arrlist_values($child_list,'id');
            $this->roleInfo['child_list'][]=$this->token['id'];
        }

        if($this->token['RoleID']==111)
        {
            $this->isAgentUser = 1;
        }
        $this->nav = $nav;
        $this->agent_first = $agent_first;
        $this->assign['_role'] = $this->roleInfo;
        $this->assign['user'] = $this->token;
        $this->assign['rid'] = get_uniqid(10);
        $this->assign['isadmin'] = $this->isadmin;
        $this->assign['col_1'] = $col_1;
        $this->assign['col_2'] = $col_2;
    }

    public function agent_child_list($id, &$child = [])
    {

        $child_list = [];
        if (is_array($id)) {
            foreach ($id as $id_s) {
                is_array($this->agent_son[$id_s]) && $child_list = array_merge($child_list, $this->agent_son[$id_s]);
            }
        } else {
            is_array($this->agent_son[$id]) && $child_list = $this->agent_son[$id];
        }

        $id = arrlist_values($child_list, 'id');

        if (!empty($id) && is_array($id)) {
            $child = empty($child) ? $child_list : array_merge((array)$child_list, (array)$child);
            $this->agent_child_list($id, $child);
        } else {
            $child = array_merge((array)$child, (array)$child_list);
        }
    }


    public function agent_child_code($id)
    {
        $child = [];
        $this->agent_child_list($id, $child);
        $data = [];
        foreach ($child as $v) {
            $v['RoleID'] == 9 && $data[] = $v['id'];
        }
        unset($child);
        return $data;
    }


    public function needadmin($admin = 0)
    {
        if ($admin == 1) {
            $arr = [1,2,3,103,104];
            !in_array($this->token['RoleID'] ,$arr) AND $this->response('0001', [], '权限不足.');
            //$this->token['RoleID'] < 1 AND $this->response('0001', [], '权限不足.');
        }
        if (!$this->isadmin) {
            $this->response('0001', [], '权限不足.');
        }
    }

    public function hspower($userid, $type = 0)
    {
        if (empty($this->token) || empty($userid)) {
            $this->response('0001', [], '账户不存在.');
        }

        if ($type == 1) {
            $user = $this->Usr->read(['usrid' => $userid], 'usrid,agent_id');
            $agent_id = $user['agent_id'];
        } else {
            $agent_id = $userid;
        }
        //echo $agent_id,' ',$userid,' ',$this->default_user['id'],"\r\n";
        if ($this->default_user['id'] && ($agent_id == $this->default_user['id'])) {
            return true;
        }
        $arr = [1,2,103,104];
        if(in_array($this->token['RoleID'] ,$arr)){
            return true;
        }

        $hs = $this->check_agent_id($agent_id, $this->default_user['id']);
        if ($hs != true) {
            $this->response('0001', [], '账户不存在.');
        }
    }

    public function check_agent_id($agent_id, $fagent_id)
    {
        if (empty($agent_id)) {
            $this->response('0001', [], '代理权限不足');
        }
        $agent['parent_id'] = isset($this->agent_key_value[$agent_id]) ? $this->agent_key_value[$agent_id] : 0;
        if ($agent_id == $fagent_id) {
            return true;
        } else {
            return $this->check_agent_id($agent['parent_id'], $fagent_id);
        }
    }

    public function findPlayer($username)
    {
        empty($username) AND $this->response('0001', [], '账户不存在.');
        if (isset($username{10}) && is_numeric($username) && $username > 11000000000) {//手机
            $cond = array('openid' => $username);
        } elseif (is_numeric($username)) {
            $cond = array('usrid' => $username);
        } else {
            $cond = array('openid' => $username);
        }
        //获取当前账号的层级
        if (!$this->isadmin) {
            $idsArr = $this->agent_child_code($this->default_user["id"]);
            if (empty($idsArr)) {
                $idsArr = [$this->default_user["id"]];
            } else {
                $idsArr[] = $this->default_user["id"];
            }
            $cond["agent_id"] = $idsArr;
        }
		$cond['ai']=0;
        $user = $this->Usr->read($cond);
        empty($user['usrid']) AND $this->response('0001', [], '账户不存在.');
        $this->hspower($user['usrid'], 1);
        return $user;
    }


    public function stat_agent_start_end($user, $start, $end)
    {
        $data = $this->MoneyTrans->stat_sum_new(date('Y-m-d 00:00:00', $start), date('Y-m-d 23:59:59', $end),$user['id']);

        return [
            "Account" => $user['username'],
            "UserShareRate" => $user['share_rate'],
            "press" => $data['Game_Bet'],
            "win" => $data['Game_Win'],
            "RateWin" => 0.000,
            "yield" => $data['Game_yield'],
            "pk_press" => $data['Poker_Bet'],
            "pk_win" => $data['Poker_Win'],
            "pk_pump" => $data['Poker_Pump'],
            "pk_yield" => $data['Poker_yield'],
            "25" => $data['Reg_Ad'],
            "29" => $data['Apply_Ad'],
            "35" => $data['Plat_Ad'],
            "36" => $data['Active_Ad']
        ];

    }

    public function stat_player_start_end($user, $start, $end,$role)
    {

        $data = $this->MoneyTrans->stat_sum_new_usr(date('Y-m-d 00:00:00', $start),date('Y-m-d 23:59:59', $end),$user['usrid']);

		if($role['dh']){
			$username = $user['openid'] . '(' . $user['usrid'] . ')';
			if(in_array($role['RoleID'],[9,10,11])){
				if(!in_array($user['agent_id'],$role['child_list'])){
					$username = $user['usrid'];
				}
			}
		}else{
			$username =  $user['usrid'];
		}

        return [

            "Account" => $username,
            "press" => $data['Game_Bet'],
            "win" => $data['Game_Win'],
            "RateWin" => 0.00,
            "yield" => $data['Game_yield'],
            "pk_press" => $data['Poker_Bet'],
            "pk_win" => $data['Poker_Win'],
            "pk_pump" => $data['Poker_Pump'],
            "pk_yield" => $data['Poker_yield'],
            "25" => $data['Reg_Ad'],
            "29" => $data['Apply_Ad'],
            "35" => $data['Plat_Ad'],
            "36" => $data['Active_Ad']
        ];

    }


}

?>