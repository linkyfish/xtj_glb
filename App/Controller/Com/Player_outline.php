<?php

namespace Com;

use Ctrl\GameController;

Class Player_outline extends GameController {

    public function Index () {
        $sid = $this->request->param('sid', '');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        $parent=$this->User->read_by_id($agent['parent_id']);
        return $this->View(get_defined_vars());
    }

    public function Index_Data(){
    	$this->needadmin();
    	$day = max(1,$this->request->param('day',7));
		$page = max(1,$this->request->param('page',1));
		$limit = max(10,$this->request->param('limit',10));
		$time = time();
		$data =$this->Usr->GetList(['offline_time'=>['<='=>strtotime(date( 'Y-m-d 23:59:59',strtotime("-{$day} day")))],'ai'=>0],['offline_time'=>-1],$page,$limit);
		$agent = $this->agent_id_name;

		$agent_arr = $this->agent_key_value;


		$_agent=[];
		foreach ($data['results'] as &$v){
			$pid = $agent_arr[$v['agent_id']];
			$pid = $agent_arr[$pid];
			$_agent[$pid] = isset($_agent[$pid]) ? $_agent[$pid]:$this->User->read_by_id($pid);
			$v['agent_balance']=$_agent[$pid]['balance'];
			if($this->roleInfo['dh']){
				$v['Account'] = $v['openid'] . '(' . $v['usrid'] . ')';
			} else {
				$v['Account'] = $v['usrid'];
			}
			$v['agent_name']=$agent[$v['agent_id']];
			$v['total_win']=calculate(-$v['total_win']);
			$v['offline_day']=ceil(($time-$v['offline_time'])/86400);
			$v['offline_time']=date('Y-m-d H:i:s',$v['offline_time']);
			unset($v['openid']);
		}

		$this->response('0000',$data);
	}
}
