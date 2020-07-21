<?php

namespace Com;

use Ctrl\GameController;

Class Com_platformreport extends GameController {

    public function Index () {
        $sid = $this->request->param('sid','');

        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        $isadmin =  $this->isadmin;
        $tpl = 'Com_platformreportuseragent.Index';
        if(in_array($this->token['RoleID'],[9,10,11]))
        {
            $tpl = 'Com_platformreport.Index';
        }
        return $this->View(get_defined_vars(),$tpl);
    }
}
