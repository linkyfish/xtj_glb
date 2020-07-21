<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_regsend extends GameController {

    public function Index () {
        $sid = $this->request->param('sid','');
        $player = $this->findPlayer($sid);
        $userlist = $this->Usr->select(['regip'=>$player['regip']]);
        return $this->View(get_defined_vars());
    }
}
