<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_supercotrolsetting extends GameController {

    public function Index () {
        $sid = $this->request->param('sid','');
        $userRes = $this->Usr->read(['usrid'=>$sid]);
        $total_win = calculate($userRes['total_win']);
        $total_uppoints = calculate($userRes['total_uppoints']) ;
        $total_cash = calculate($userRes['total_cash']);
        $list = $this->SuperControlList->select(['player_id'=>$sid],'start_val asc ');
        return $this->View(get_defined_vars());
    }
}
