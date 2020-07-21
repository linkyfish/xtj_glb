<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_setscore extends GameController {

    public function Index () {
        $sid = $this->request->param('sid','');
        $player = $this->findPlayer($sid);
        return $this->View(get_defined_vars());
    }
}
