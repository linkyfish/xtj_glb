<?php

namespace Com;

use Ctrl\GameController;

Class Com_regscore extends GameController {

    public function Index () {
        $agent = $this->default_user;
        $agent['reg_mark']=calculate($agent['reg_mark']);
        return $this->View(get_defined_vars());
    }

    public function Index_new_player(){

        $cond['agent_id'] = $this->default_user['id'];
        empty( $cond['agent_id']) && $cond['agent_id']=$this->token['id'];
        $cond['regtime'] = ['>='=>date('Y-m-d 00:00:00'),'<='=>date('Y-m-d H:i:s')];
        $cond['reg_give']=0;
        $data = $this->Usr->select($cond,[],'usrid,openid,regtime,reg_give');
        $this->response('0000',['data'=>$data]);
    }

    public function Index_send(){
        //'agent_id'=>$this->default_user['id'],
        $usrid = $this->request->param('usrid',0);
        $type = $this->request->param('type',0);
        $usr = $this->Usr->read(['usrid'=>$usrid]);
        $this->hspower($usr['usrid'],1);
        if($usr['reg_give']!=0){
            $this->response('0001',[],'注册送已'.($usr['reg_give']==1?'赠送':'忽略'));
        }
        if(!$this->isadmin){
            $agent=$this->token;
            $usr['agent_id']!=$agent['id'] AND $this->response('0001',[],'直接代理才可以赠送');
        }else{
            $agent=$this->User->read_by_id($usr['agent_id']);
        }

        ($agent['is_reg_give']!=1||$agent['reg_set']<1) AND $this->response('0001',[],'注册送未开启');
        empty($agent['reg_mark']) && $this->response('0001',[],'注册送分值未设定');
		$time=time();
		$key = 'regsend_lock_' . $usr['usrid'];
		$islock = $this->User->CacheGet($key);
		if (!empty($islock['time'])) {
			$this->response('0001', [], '操作太快');
		}
		$this->User->CacheSet($key,['time'=>$time],120);
        if($type==2){
            $this->Usr->update(['usrid'=>$usrid],['reg_give'=>2]);
            $this->OperateLog->Add($this->token['id'],$usr['usrid'],0,$this->request->get_client_ip(1),'注册送忽略');
			$this->User->CacheDel($key);
        }elseif($type==1){
        	$this->Gamebilog->Add($agent['reg_mark'],25,$usr['usrid'],$agent['id'],$this->request->get_client_ip(1),time(),$this->token['id']);
			$this->Usr->update(['usrid'=>$usrid],['reg_give'=>1]);
			$this->User->update(['id'=>$agent['id']],['reg_set-'=>1,'reg_give_num+'=>1]);
			$this->User->CacheDel($key);
        }
        $this->response('0000');
    }


}
