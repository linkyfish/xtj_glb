<?php

namespace Com;

use Ctrl\GameController;

Class Changepassword extends GameController {

    public function Index () {
        $isFirst=0;
        return $this->View(get_defined_vars());
    }
}
