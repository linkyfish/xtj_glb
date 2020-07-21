<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_adminwhitelist extends GameController {

    public function Index () {
        $sid = $this->request->param('sid','');
        $list =  $this->AdminIpWhiteList->select();
        return $this->View(get_defined_vars());
    }
}
