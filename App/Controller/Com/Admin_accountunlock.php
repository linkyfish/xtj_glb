<?php

namespace Com;

use Ctrl\GameController;

Class Admin_accountunlock extends GameController {

    public function Index () {
        $this->needadmin();
        return $this->View();
    }
}
