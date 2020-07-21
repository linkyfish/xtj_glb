<?php

namespace Com;

use Ctrl\GameController;

Class Com_setscore_index extends GameController {

    public function Index () {
        $sid = $this->request->param('sid','');
        $this->needadmin();
        $sid AND $this->findPlayer($sid);
        return $this->View(get_defined_vars());
    }

    public function Index_setServerScore () {
        $sid = $this->request->param('sid','');
        $this->needadmin();
        $sid && $this->findPlayer($sid);
        return $this->View(get_defined_vars(),'Com_setscore_index.Index');
    }
}
