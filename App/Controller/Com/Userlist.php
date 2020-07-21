<?php

namespace Com;

use Ctrl\GameController;

Class Userlist extends GameController
{

    public function Index()
    {
        if($this->token['RoleID']==101){
            $this->response('0000',[],'','../../com/searchUser.aspx',302);
        }

        $sid = $this->request->param('sid', '');
        $agent = $sid ? $this->User->read_by_username($sid) : $this->default_user;
        $this->hspower($agent['id']);
        //$agent["code"]=str_pad( $agent['code'],6,0,STR_PAD_LEFT);

		$ids = $this->agent_child_code($agent["id"]);
		$ids[] = $agent["id"];
		$key = 'money_total_' . md5(xn_json_encode($ids));
		$num = $this->Usr->CacheGet($key);
		if (!$num) {
			$num = $this->Usr->sum('chips+bankchips', ['agent_id' => $ids]);
			$this->Usr->CacheSet($key, $num, 60);
		}

        $parent = $agent['parent_id'] ? $this->User->read_by_id($agent['parent_id']) : [];
        return $this->View(get_defined_vars());
    }

}
