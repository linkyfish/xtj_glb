<?php

namespace Com;

use Ctrl\GameController;

Class Playerlist extends GameController
{

    public function Index()
    {

        $sid = $this->request->param('sid', '-1');
        $aid = $this->request->param('aid', '');
        if(!empty($aid))
        {
            $agent = $this->Usr->read(['usrid'=>$aid]);
        }
        else{
            $agent = $this->Usr->read(['openid'=>$sid]);
        }

        //$this->hspower($agent['id']);
        if(!isset($agent['usrid']))
        {
            $agent = ['id'=>'','username'=>'-1'];
            $parent = ['id'=>'','username'=>'-1'];
        }
        else{
            $parent['username'] = $agent['openid'];
        }

        return $this->View(get_defined_vars());
    }

}
