<?php

namespace Com;

use Ctrl\GameController;

Class Chart_user extends GameController {

    public function Index () {
        $sid= $this->request->param('sid','');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        return $this->View(get_defined_vars());
    }
    public function Index_AgentChart () {
        $sid= $this->request->param('sid','');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $UTYPE= 'AgentReport';
        $this->hspower($agent['id']);
        $parent = $this->User->read_by_id($agent['parent_id']);
        return $this->View(get_defined_vars(),'Chart_user.Index');
    }
}
