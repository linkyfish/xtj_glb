<?php

namespace Com;

use Ctrl\GameController;

Class     Admin_payment_orderlist extends GameController {

    public function Index () {
        $this->needadmin();
        $payment = $this->PayConfig->select();
        return $this->View(get_defined_vars());
    }


    public function  cashlog()
    {
        $this->needadmin();
        return $this->View(get_defined_vars());
    }



}
