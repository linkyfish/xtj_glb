<?php

namespace Com;

use Ctrl\GameController;

Class Sys_admin_iplist extends GameController
{

    public function Index()
    {
        $this->needadmin();
        return $this->View(get_defined_vars());
    }


}
