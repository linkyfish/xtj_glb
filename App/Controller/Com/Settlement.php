<?php

namespace Com;

use Ctrl\GameController;

Class Settlement extends GameController
{

    public function Index()
    {
        return $this->View(get_defined_vars());
    }

    public function Index_cash_confirm(){
        $this->needadmin();
        $Ymd = $this->request->param('Ymd');
        $AgentID = $this->request->param('AgentID');
        $this->CheckEmpty([$Ymd,$AgentID],['结算日期','代理账户']);
        $Settlement= $this->UserSettlement->read(['Ymd'=>$Ymd,'AgentID'=>$AgentID]);
        if($Settlement['Status']!=1){
            $this->response('0001',[],'状态不是待审核');
        }
        if($Settlement['Num']<1){
            $this->response('0001',[],'状态不是待审核');
        }
        $UsrID=$Settlement['UsrID'];
        $player = $this->Usr->read(['usrid'=>$UsrID]);
        empty($player['usrid']) AND $this->response('0001',[],'收益账户不存在');
		$this->UserSettlement->update(['Ymd'=>$Ymd,'AgentID'=>$AgentID],['Status'=>2,'AdminID'=>$this->token['id'],'PayAT'=>time()]);
        $this->Gamebilog->Add($Settlement['Num'],71,$UsrID,$AgentID,$this->request->get_client_ip(1),time(),$this->token['id']);
        $this->response('0000');
    }

}
