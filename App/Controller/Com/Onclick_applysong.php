<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_applysong extends GameController {

    public function Index () {
        $sid = $this->request->param('logid',0);
        $apply = $this->ApplyLog->read(['LogID'=>$sid]);
        empty($apply['LogID']) && $this->response('0001',[],'审核订单不存在');
        $user=$this->Usr->read(['usrid'=>$apply['UsrID']]);
        $agent = $this->agent_id_name[$apply['AgentID']];
		$apply['UserName'] = isset($user) ? $user['openid'].'' : '-';
		$apply['AgentName'] = isset($agent) ? $agent : '-';
		$apply['CreateAT'] = date('Y-m-d H:i:s', $apply['AddTime']);
		$apply['Money'] = calculate($apply['Money']);
		$apply['ApplyMoney'] = calculate($apply['ApplyMoney']);
		$apply['IP'] = long2ip($apply['IP']);
		$apply['Status_fmt']=$this->ApplyLog->Status[$apply['Status']];
        return $this->View(get_defined_vars());
    }
}
