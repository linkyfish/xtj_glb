<?php

namespace Com;

use Ctrl\GameController;

Class Log_gamedetail extends GameController {

    public function Index () {
        $UserId = $this->request->param('UserId',0);
        $GameId = $this->request->param('GameId',0);
        $recordIndex = $this->request->param('RecordIndex',0);
        $this->CheckEmpty([$UserId,$GameId,$recordIndex],['玩家','游戏','局数']);
        $player = $this->findPlayer($UserId);

        $gamename = $this->Game->getNameById($GameId);
        //斗地主炸金花详情在另外的表
        $showGameArr = [620001,620002,620003];
        if(in_array($GameId,$showGameArr))
        {
            $detail = $this->ShowGameResult->GetDetail($UserId,$GameId,$recordIndex);
        }
        else{
            $detail = $this->ShowBairenResult->GetDetail($UserId,$GameId,$recordIndex);
        }
        //$tpl='game_detail_0';
        return $this->View(get_defined_vars());
    }
}
