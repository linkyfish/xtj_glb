<?php

namespace Com;

use Ctrl\GameController;

Class Com_setscoreindex extends GameController {

    public function Index () {
        $this->needadmin();
        $sid = $this->request->param('sid','');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        return $this->View(get_defined_vars());
    }
    public function Index_setServerScore () {
        $this->needadmin();
        $sid = $this->request->param('sid','');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        return $this->View(get_defined_vars(),'Com_setscoreindex.Index');
    }
}
