<?php

namespace Com;

use Ctrl\GameController;

Class Admin_smschannel extends GameController
{



    public function Index()
    {
        $this->needadmin(1);
        $payconfig = $this->PayConfig->select();
        return $this->View(get_defined_vars(),'Admin_smschannel.channellist');
    }

}
