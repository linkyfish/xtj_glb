<?php

namespace Com;

use Ctrl\GameController;

Class Com_playermaxwin extends GameController {

    public function Index () {
        $this->needadmin();
        return $this->View(get_defined_vars());
    }
}
