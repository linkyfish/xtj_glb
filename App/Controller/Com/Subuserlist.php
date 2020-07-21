<?php

namespace Com;

use Ctrl\GameController;

Class Subuserlist extends GameController {

    public function Index () {
        $role =$this->RoleNew->select(['IsEnable'=>1]);
        $role = arrlist_multisort($role,'RoleID');
        unset($role[0]);
        $role = array_values($role);
        return $this->View(get_defined_vars());
    }
}
