<?php
namespace Model;

// hook model_show_bairen_result_use.php

use App\Model;

class ShowGameResultModel extends Model
{
    // hook model_show_bairen_result_use.php
    public $table = 'show_game_result';
    public $index = 'id';
    public function GetDetail($UserId,$GameId,$gameNumb){

        $res = $this->read(['gameNumb'=>$gameNumb,'gameid'=>$GameId]);
        $res['result'] = json_decode($res['result']);
        switch ($GameId){
            case 620001://炸金花
                $res['tpl']=620001;
                return $this->getZjh($res,$UserId);
            default:
                return false;
        }
    }

    public function  getZjh($res,$UserId)
    {
        //{"ResultTemp":null,"RoomType":620001,"PlayerBoard":[[50,48,31],[9,8,2],[36,30,29],[0,0,0],[0,0,0]],"PlayerWin":"2","LostUser":0,"WinReward":"363"}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $col1 = '<tr>';
        $col1.= '<td>萤家牌</td>';
        $col1.= '<td>';
        $winBoard = $betObj->PlayerBoard[$betObj->PlayerWin] ;

        for ($n=0;$n < 3 ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker_new/POKER'.$winBoard[$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';
        $col1.='</tr>';

        $playerIndex = 0;
        $idArr = explode(',',$res->playid);
        $playerCount = count($idArr);
        for ($n=0;$n < $playerCount ;$n++ )
        {
            if($idArr[$n] ==  $UserId)
            {
                $playerIndex = $n;
            }
        }
        $col1 .= '<tr>';
        $col1.= '<td>玩家牌</td>';
        $col1.= '<td>';
        for ($n=0;$n < 3 ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker_new/POKER'.$betObj->PlayerBoard[$playerIndex][$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1 .= '<tr style="color: red">';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>';

        if($playerIndex == $betObj->PlayerWin )
        {
            $col1.= $betObj->WinReward;
        }
        else{
            $col1.= 0;
        }
        $col1.= '</td>';
        $col1.='</tr>';


        $col1 .= '<tr>';
        $col1.= '<td>输家牌</td>';
        $col1.= '<td>';
        for ($n=0;$n < 3 ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker_new/POKER'.$betObj->PlayerBoard[$betObj->LostUser][$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1 .= '<tr style="color: red">';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>';
        if($playerIndex == $betObj->LostUser )
        {
            $col1.= $betObj->WinReward;
        }
        else{
            $col1.= 0;
        }
        $col1.= '</td>';
        $col1.='</tr>';


        $res['view_html'].=$col1;
        return $res;

    }

    public function  getDdz($res)
    {
        //{"ResultTemp":null,"RoomType":620001,"PlayerBoard":[[5,2,29],[8,31,3],[25,50,1],[4,48,38],[0,0,0]],"PlayerWin":"0","WinReward":"17"}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $col1 = '<tr>';
        $col1.= '<td>萤家牌</td>';
        $col1.= '<td>';
        $winBoard = $betObj->PlayerBoard[$betObj->PlayerWin] ;
        $col1.= $winBoard[0][0];

        for ($n=0;$n < 3 ;$n++ )
        {
            $col1.= '<span>'.json_encode($betObj->PlayerBoard).'</span>';
            //$col1.= '<span><img src="../../gameImg/poker/POKER'.$winBoard[$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        return $res;

    }


}


?>