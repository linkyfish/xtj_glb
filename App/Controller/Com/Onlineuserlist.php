<?php

namespace Com;

use Ctrl\GameController;

Class Onlineuserlist extends GameController {

    public function Index () {
    	$gameid = $this->request->param('gameid',0);
		$gamelist=$this->Game->game_op;
        $tpl = 'Onlineuserlistuseragent.Index';
        if(in_array($this->token['RoleID'],[9,10,11]))
        {
            $tpl = 'Onlineuserlist.Index';
        }
        return $this->View(get_defined_vars(),$tpl);
    }
}
