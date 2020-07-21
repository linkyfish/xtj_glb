<?php

namespace Com;

use Ctrl\GameController;

Class Membermonitor extends GameController {

    public function Index () {
        $this->needadmin();
        $custorm = $this->User->select(['RoleID'=>101]);
        $custorm = arrlist_key_values($custorm,'username','username');
        return $this->View(get_defined_vars());
    }

}
