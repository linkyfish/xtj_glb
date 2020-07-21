<?php

namespace Com;

use Ctrl\GameController;

Class Sys_rule extends GameController
{

    public function Index()
    {
        $this->needadmin(1);
        return $this->View(get_defined_vars());
    }

}
