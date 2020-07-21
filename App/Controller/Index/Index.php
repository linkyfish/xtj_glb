<?php

namespace Index;

use Ctrl\Controller;
use Task\Task;

// hook index_index_use.php

Class Index extends Controller
{

    // hook index_index_start.php

    public function Index()
    {
//        $agent = $this->User->select([], [], 'id,parent_id,share_rate,RoleID,username');
//        $agent_id_value = arrlist_change_key($agent, 'id');
//        $user_id_agent_id = arrlist_key_values($this->Usr->select([], [], 'usrid,agent_id'), 'usrid', 'agent_id');
//
//        $list = $this->MoneyTrans->select([], [], 'usrid,chips,style,diamond,gameID');
//        $groups = arrlist_group($list, 'usrid');
//        $agent_array = [];
//        foreach ($groups as $usrid => $group) {
//            $agent_id = $user_id_agent_id[$usrid];
//
//            if (empty($agent_id)) {
//                xn_log($usrid . ' No AgentID', 'settlement');
//                continue;
//            }
//
//            $row = $this->MoneyTrans->getZhangDataItem($group);
//            $agent_array[$agent_id] = [
//                "Game_Bet" => bcadd($agent_array[$agent_id]['Game_Bet'], $row['Game_Bet']),
//                "Poker_Pump" => bcadd($agent_array[$agent_id]['Poker_Pump'], $row['Poker_Pump']),
//                "Reg_Ad" => bcadd($agent_array[$agent_id]['Reg_Ad'], $row['Reg_Ad']),
//                "Active_Ad" => bcadd($agent_array[$agent_id]['Active_Ad'], $row['Active_Ad'])
//            ];
//        }
//        $time = time();
//        $ymd = date('Ymd');
//        foreach ($agent_array as $k => $v) {
//            $js = $this->UserStat->read(['Ymd' => $ymd, 'AgentID' => $k]);
//            if ($js['Ymd']) {
//                continue;
//            }
//            $parent_id=$agent_id_value[$k]['parent_id'];//主号
//            $zhid=$agent_id_value[$parent_id]['parent_id'];//管理号
//            $glh=$agent_id_value[$zhid];//管理号
//            $row = [
//                'Ymd' => $ymd,
//                'AgentID' => $k,
//                'ParentID' => $agent_id_value[$k]['parent_id'],
//                'Game_Bet' => $v['Game_Bet'],
//                'Poker_Pump' => $v['Poker_Pump'],
//                'Reg_Ad' => $v['Reg_Ad'],
//                'Active_Ad' => $v['Active_Ad'],
//                'Rate' => $glh['share_rate'],
//                'GLH' => $glh['id'],
//                'CreateAT' => $time,
//            ];
//            $this->UserStat->insert($row);
//        }
//
//
//        return json_encode($agent_array);

//        $start = memory_get_usage();
//        $row = ['usrid' => 0, 'chips' => 0, 'style' => 0, 'diamond' => 0, 'gameID' => 0];
//        $arr = [];
//        for ($i = 0; $i < 30000000; $i++) {
//            $arr[] = $row;
//        }
//        return bcdiv(bcsub(memory_get_usage(), $start), 1024*1024, 4) . 'Mb';
        $isagent =   $this->request->_S("AGENT");
        return $this->View(get_defined_vars());
    }

    public function cash()
    {
        $get = $this->request->get;
        $get = http_build_query($get);
        $this->response('0000', '', '', '../?' . $get, 302);
    }

    public function redo(){
		$this->PostTask('redo', ['start' => 1]);
		return '等待';
	}

    public function share()
    {
        $get = $this->request->get;
        $get = http_build_query($get);
        $this->response('0000', '', '', '../?' . $get, 302);
    }
    // hook index_index_end.php
}
