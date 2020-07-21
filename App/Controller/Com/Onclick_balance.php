<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_balance extends GameController
{

    public function Index()
    {
        $user = $this->request->param('sid', $this->agent_info['AgentID'], 'intval');
        $this->hspower($user);
        empty($user) AND $this->response('0001',[],'代理不存在');
        $agent_info = $this->Agent->read(['AgentID' => $user]);
        $profit_arr = $this->get_agent_profit($user);
        //处理中的收益

        $apply_profit = floor($this->AgentBill->sum('Gamebi',['AgentID' => $user, 'Status' => 1]));
        //已结算的收益
        $already_profit = floor($this->AgentBill->sum('Gamebi',['AgentID' => $user, 'Status' => 2]));
        //未结算收益
        $no_profit = $profit_arr['total_profit'] - $apply_profit - $already_profit;
        return $this->View(get_defined_vars());
    }


    public function get_agent_profit($agentid)
    {
        empty($agentid) AND $this->response('0001',[],'代理不存在');

        $agent_info =$this->Agent->read(['AgentID' => $agentid]);
        $total_profit = 0;
        $total_num = 0;
        //下级代理
        $child_list =$this->Agent->select(['ParentID' => $agentid]);

        $total_gamebi = 0;
        $agent_gamebi = 0;
        $agent_profit = 0;
        if (!empty($child_list)) {
            foreach ($child_list as $k => $v) {
                $child_arr = [];
                $this->agent_child_list($v['AgentID'], $child_arr);
                $child_arr = arrlist_values($child_arr, 'AgentID');
                $child_arr[] = $v['AgentID'];

                if (!empty($child_arr)) {
                    $child_gamebi = $this->get_agent_gamebi(implode(',', $child_arr), $this->bl);
                } else {
                    $child_gamebi = 0;
                }
                $total_gamebi += $child_gamebi;
                $my_rate = intval($agent_info['ServiceFee'] - $v['ServiceFee']) / 100;
                $child_profit = $my_rate * $child_gamebi * $this->bl ;
                $total_profit += $child_profit;
                $agent_gamebi += $child_gamebi;
                $agent_profit += $child_profit;

            }
        }
        //自己名下的

        $my_gamebi = $this->get_agent_gamebi($agentid, $this->bl);
        $my_profit = $my_gamebi * ($agent_info['ServiceFee'] / 100) * $this->bl;
        //echo $total_profit,' ',$my_profit;
        $total_profit = 0 - $total_profit;
        $total_profit -= $my_profit;
        $total_gamebi += $my_gamebi;
        $my_profit = 0 - $my_profit;
        $agent_profit = 0 - $agent_profit;

        return array('total_profit' => substr(sprintf("%.3f", $total_profit), 0, -1), 'total_gamebi' => substr(sprintf("%.3f", $total_gamebi), 0, -1), 'agent_profit' => substr(sprintf("%.3f", $agent_profit), 0, -1), 'agent_gamebi' => substr(sprintf("%.3f", 0 - $agent_gamebi), 0, -1), 'my_profit' => substr(sprintf("%.3f", $my_profit), 0, -1), 'my_gamebi' => substr(sprintf("%.3f", 0 - $my_gamebi), 0, -1));
    }

    public function get_agent_gamebi($agent_str, $ExcjamgeRate)
    {

        $result = $this->gameapi->get_agent_total($agent_str);
        $gamebinum = 0;
        if ($result['msg'] == 'succeed') {
            $gamebinum = $result['gamebinum'];
        }
        $gamebinum = $gamebinum / 10000 / $ExcjamgeRate /$this->rmb;
        return $gamebinum;
    }
}
