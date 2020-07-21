<?php

namespace Com;

use Ctrl\GameController;

Class Report_agenttotal extends GameController {

    public function Index () {
        $sid= $this->request->param('sid','');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        return $this->View(get_defined_vars());
    }

    public function Index_AgentTotalReport(){
        $sid = $this->request->param('sid','');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        return $this->View(get_defined_vars(),'Report_agenttotal.Index');
    }

    public function Index_ServerTotalReport(){
        $sid = $this->request->param('sid','');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        return $this->View(get_defined_vars(),'Report_agenttotal.Index');
    }

    public function Index_HistorySettle(){
        $sid = $this->request->param('sid','');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        return $this->View(get_defined_vars(),'Report_agenttotal.HistorySettle');
    }

}
