<?php

namespace Com;

use Ctrl\GameController;

Class Report_account extends GameController
{

    public function Index()
    {
        $idd = $this->request->param('idd', '');
        $fagent = $agent = $idd ? $this->User->read_by_username($idd):$this->default_user;
        $this->hspower($agent['id']);
        return $this->View(get_defined_vars());
    }


    public function Index_AgentReport()
    {
        $idd = $this->request->param('idd', '');
        $fagent = $agent = $idd ? $this->User->read_by_username($idd):$this->default_user;
        $this->hspower($agent['id']);
        $UTYPE = 'AgentReport';
        return $this->View(get_defined_vars(), 'Report_account.Index');
    }

    public function Index_ServerReport()
    {
        $idd = trim($this->request->param('idd', ''));
		if (isset($idd{10})) {
			$agent = $this->Account->read_one(['account' => $idd]);
		} else {
			$agent = $this->Account->read_one(['usrid' => $idd]);
		}

        $this->hspower($agent['usrid'], 1);
        $agent['username'] = $agent['account'];
        $fagent = $this->User->read_by_id($agent['agent_id']);
        $UTYPE = 'ServerReport';
        return $this->View(get_defined_vars(), 'Report_account.Index');
    }


}
