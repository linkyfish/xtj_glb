<?php

namespace Com;

use Ctrl\GameController;

Class Room extends GameController {

    public function Index () {
        $this->needadmin(1);
        return $this->View(get_defined_vars());
    }

}
