<?php

namespace Com;

use Ctrl\GameController;

Class Com_custom_edit extends GameController {

    public function Index () {
        return $this->View(get_defined_vars());
    }


}
