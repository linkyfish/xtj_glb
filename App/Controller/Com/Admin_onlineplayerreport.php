<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019-10-20
 * Time: 23:36
 */

namespace Com;


use Ctrl\GameController;

class Admin_onlineplayerreport extends GameController {

    public function Index () {
        $this->needadmin();
        return $this->View();
    }
}