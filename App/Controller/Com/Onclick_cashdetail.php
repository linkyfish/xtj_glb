<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_cashdetail extends GameController {

    public function Index () {
        $id = $this->request->param('id',0);
        $detail = $this->CashLog->read(['LogID'=>$id],'LogID,AgentID,BankUser as BankOwner,Money as RealCashNum,BankCard as BankNum,BankName');
        empty($detail['LogID']) AND $this->response('0001',[],'订单不存在');
        $sid=$this->request->param("sid");
        $order_user = $this->findPlayer($sid);
        $money_user = $this->User->read(['game_user_id'=>$order_user['usrid']]);
        $agent_arr = $this->agent_key_value;
        $pid = $agent_arr[$order_user['agent_id']];
        $pid = $agent_arr[$pid];
        $agent = $this->User->read_by_id($pid);
        $gamelist=$this->Game->game_op;
        return $this->View(get_defined_vars());
    }
}
