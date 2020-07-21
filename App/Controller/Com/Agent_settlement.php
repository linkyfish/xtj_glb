<?php

namespace Com;

use Ctrl\GameController;

Class Agent_settlement extends GameController
{

    public function Index()
    {
        $agent = $this->default_user;
        return $this->View(get_defined_vars());
    }

    public function Index_cash(){
        $ymd = $this->request->param('ymd',0);
        empty($ymd) AND $this->response('0001');
        $cash = $this->UserSettlement->read(['Ymd'=>$ymd,'AgentID'=>$this->token['id']]);
        if($cash['Ymd'] && $cash['Status']==0){
            $this->OperateLog->Add($this->token['id'],$this->default_user['id'],1,$this->request->get_client_ip(1),'申请结算');
            $this->UserSettlement->update(['Ymd'=>$ymd,'AgentID'=>$this->token['id']],['Status'=>1,'SubAT'=>time()]);
            $this->response('0000');
        }else{
            $this->response('0001',[],'已确认');
        }

    }

    public function Index_bind(){
        $username = $this->request->param('sid','');
        empty($username) AND $this->response('0001',[],'请填写,收益账号.');
        $this->default_user['RoleID']!=11 AND $this->response('0001',[],'自主结算仅针对管理号开放.');
        !empty($this->token['game_user_id']) AND $this->response('0001',[],'您已绑定收益账户，如需修改请联系客服.');
        if (isset($username{10}) && is_numeric($username) && $username > 11000000000) {//手机
            $cond = array('openid' => $username);
        } elseif (is_numeric($username)) {
            $cond = array('usrid' => $username);
        } else {
            $cond = array('openid' => $username);
        }
        $user = $this->Usr->read($cond);
        empty($user['usrid']) AND $this->response('0001',[],'收益账号不存在.');
        $lock_agent = $this->User->read(['game_user_id'=>$user['usrid']]);
        !empty($lock_agent['id']) AND $this->response('0001',[],'收益账号已绑定其他代理.');

        $agent_key_value = $this->agent_key_value;
        $agent_zhu = $agent_key_value[$user['agent_id']];
        $agent_guanli = $agent_key_value[$agent_zhu];
        $agent_guanli!=$this->token['id'] AND $this->response('0001',[],'仅能绑定自己主号推广号下的玩家账户.');
        $this->OperateLog->Add($this->token['id'],$user['usrid'],0,$this->request->get_client_ip(1),'绑定收益账户');
        $this->User->update(['id'=>$this->token['id']],['game_user_id'=>$user['usrid']]);
        $this->UserSettlement->update(['AgentID'=>$this->token['id'],'UsrID'=>0,'is_delete'=>[0,1]],['UsrID'=>$user['usrid']]);
        $this->response('0000');
    }

}
