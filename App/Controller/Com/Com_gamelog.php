<?php

namespace Com;

use Ctrl\GameController;

Class Com_gamelog extends GameController {

    public function Index () {
        $gamelist=$this->Game->game_op;
        return $this->View(get_defined_vars());
    }
}
