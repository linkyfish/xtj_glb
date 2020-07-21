<?php

namespace Ashx;

use Ctrl\GameController;

Class Common extends GameController
{

    public function Checklogin_CheckLogin()
    {
        $this->response('0000', [], 'LoginOK.');
    }

}
