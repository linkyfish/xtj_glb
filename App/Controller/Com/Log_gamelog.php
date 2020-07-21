<?php

namespace Com;

use Ctrl\GameController;

Class Log_gamelog extends GameController
{

    public function Index()
    {
        $sid= $this->request->param('sid','');
        $agent = $this->findPlayer($sid);
        $gamelist=$this->Game->game_op;
        return $this->View(get_defined_vars());
    }



}
