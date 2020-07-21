<?php
namespace Model;

// hook model_show_bairen_result_use.php

use App\Model;

class ShowBairenResultModel extends Model
{
    // hook model_show_bairen_result_use.php
    public $table = 'show_bairen_result';
    public $index = 'id';

    //public $is_delete = 'is_delete';
    // hook model_show_bairen_result_start.php


    public function GetDetail($UserId,$GameId,$gameNumb){

        $res = $this->read(['gameid'=>$GameId,'gameNumb'=>$gameNumb,'playid'=>$UserId]);
        $res['result'] = json_decode($res['result']);

        switch ($GameId){
            case 10001: //百家乐
            case 10002:
            case 10003:
                $res['tpl']=10001;
                return $this->G10001($res);
            case 30004://水果机
                $res['tpl']=30004;
                return $this->G30004($res);
            case 30005://croods
                $res['tpl']=30005;
                return $this->G30005($res);
            case 20001://黑杰克
            case 20002:
            case 20003:
                $res['tpl']=20001;
                return $this->getHjkDetail($res);
            case 30006://金瓶梅
                $res['tpl']=30006;
                return $this->G30006($res);
            case 32002://玉蒲团
                $res['tpl']=30002;
                return $this->G32002($res);
            case 30007://僵尸
                $res['tpl']=30007;
                return $this->G30007($res);
                break;
            case 32004://财神到
                $res['tpl']=32004;
                return $this->G32004($res);
                break;
            case 32006://水浒传
                $res['tpl']=32004;
                return $this->G32006($res);
                break;
            case 30008://招财进宝
                $res['tpl']=30008;
                return $this->G30008($res);
            case 32001://大吉大利
            case 32002://大吉大利'
                $res['tpl']=32001;
                return $this->G32001($res);
                break;
            case 32005://舞狮争霸
                $res['tpl']=32005;
                return $this->G32005($res);
            case 32007://五龙争霸
                $res['tpl']=32007;
                return $this->G32007($res);
            case 51001://连环夺宝
                $res['tpl']=51001;
                return $this->G51001($res);
            case 53001://跑马
                $res['tpl']=53001;
                return $this->G53001($res);
            case 70001://百人牛牛
            case 70002:
            case 70003:
                $res['tpl']=70001;
                return $this->G70001($res);
            case 80001://红黑大战
            case 80002:
            case 80003:
                $res['tpl']=80001;
                return $this->G80001($res);
//            case 100002://seak
//            case 100004:
//            case 100005:
//                $this->G100002($res);
//                break;
            case 200001://疯狂水果
                $res['tpl']=200001;
                return $this->G200001($res);
            case 200002://极速时刻
                $res['tpl']=200002;
                return $this->G200002($res);
            case 200003://魔法宝石
                $res['tpl']=200003;
                return $this->G200003($res);
            case 200005://五星宏辉
                $res['tpl']=200005;
                return $this->G200005($res);
            case 200006://飞禽走兽
                $res['tpl']=200006;
                return $this->G200006($res);
            case 52001://经典水果机
                $res['tpl']=52001;
                return $this->G52001($res);
            case 680001://龙虎
            case 680002:
            case 680003:
                $res['tpl']=680001;
                return $this->G680001($res);
            case 40001:
                $res['tpl']=40001;
                return $this->getFpjDetail($res);
            case 90001:
                $res['tpl']=90001;
                return $this->G90001($res);
            default:
                return false;
        }
    }

    /**
     * 五星宏辉开奖结果
     * @param $res
     * @return array
     */
    public function  G200005($res)
    {

        $betObj = $res['result'];
        $winObj = $betObj->RewardArea;


        $res['view_html'] = '';
        $col1 ='<tr >';

        if($winObj[0] == 53)
        {
            $col1.= '<td colspan="5"><img src="../../gameImg/poker/POKERa.png" style="width: 60px;height: 60px;"></td>';
        }
        else if($winObj[0] == 54)
        {
            $col1.= '<td colspan="5"><img src="../../gameImg/poker/POKERb.png" style="width: 60px;height: 60px;"></td>';
        }
        else{
            $col1.= '<td colspan="5"><img src="../../gameImg/poker/POKER'.$winObj[0].'.png" style="width: 60px;height: 60px;"></td>';
        }
        $col1.='</tr>';
        $col1 .='<tr style="text-align: center;background-color: #2b2b2b;color: White">';
        $col1.= '<td>信息</td>';
        $col1.= '<td>黑桃</td>';
        $col1.= '<td>红桃</td>';
        $col1.= '<td>梅花</td>';
        $col1.= '<td>方块</td>';
        $col1.= '<td>大小王</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        $col1 ='<tr style="color: green">';
        $col1.= '<td>压住</td>';
        for ($i= 0 ; $i < 5;$i++ )
        {
            $col1.= '<td>';
            $col1.= calculate($betObj->BetArea[$i]);
            $col1.= '</td>';
        }
        $res['view_html'].=$col1;
        $peilv = [3.8,3.8,4,4];
        $col1 ='</tr>';
        $col1 .='<tr style="color:red">';
        $col1.= '<td>萤分</td>';
        for ($i= 0 ; $i < 5;$i++ )
        {
            $col1.= '<td>';
            if($i == $winObj[1] )
            {
                $col1.= calculate($betObj->PlayerReward); //calculate($betObj->BetArea[$i])*$peilv[$i];
            }
            else{
                $col1.= 0;
            }
            $col1.= '</td>';
        }
        $col1.='</tr>';
        $res['view_html'].=$col1;
        return $res;
    }



    /**
     * 水果机开奖结果
     * @param $res
     * @return array
     */
    public function  G30004($res)
    {

        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        for ($i=0;$i < 3 ;$i++ )
        {
            $col1 ='<tr>';
            $col1.='<td colspan="3">';
            $col1.='<div style="text-align: center; margin: 0 auto">';
            for ($n=0;$n < 5 ;$n++ )
            {
                $col1.= '<span><img src="../../gameImg/labaji/fruit/ft'.$retObj[$i][$n].'.png" style="width: 60px;height: 60px;"></span>';
            }
            $col1.='</div>';
            $col1.='</td></tr>';
            $res['view_html'].=$col1;
        }

        $res['view_html'].= $this->getLineScoreList($betObj,30);
        return   $res;
    }

    /**
     * 经典水果机
     * @param $res
     * @return array
     */
    public function  G52001($res)
    {

        //{"ResultTemp":null,"AreaResult":[[18,2000,2,4000]],"BetArea":[2000,3000,3000,2000,3000,2000,2000,1000]}

        $res['view_html'] = '';
        $betObj = $res['result'];
        $gameShow =  $betObj->AreaResult;
        $posArr = [5,16,20,8,2,7,1,6];
        $imgArr = ['0'=>0, '1'=>1, '2'=>2, '3'=>3, '4'=>4, '5'=>5, '6'=>6, '7'=>7, '8'=>8, '9'=>9, '10'=>10, '11'=>6, '12'=>12, '13'=>1, '14'=>2, '15'=>15, '16'=>16, '17'=>6,
            '18'=>18,
            '19'=>7,
            '20'=>20,
            '21'=>21,
            '22'=>22,
            '23'=>6,
        ]   ;
        $col1='<tr>';
        $col1 .= '<td>区域</td>';
        for ($n=0;$n < 8 ;$n++ ) {
            $col1.= '<td><img src="../../gameImg/bairenyouxi/52001/lottery'.$posArr[$n].'.png?id='.time().'" style="width: 40px;height: 40px;"></td>';
        }
        $col1.='</tr>';
        $res['view_html'].=$col1;
        $col1 ='<tr style="color: green">';
        $col1 .= '<td>压住</td>';
        for ($n=0;$n < 8 ;$n++ ) {
            $col1 .= '<td>' . calculate($betObj->BetArea[$n]) . '</td>';
        }
        $col1.='</tr>';

        $resultCount = count($gameShow);
        $totalWin = 0;
        for ($n=0;$n < $resultCount ;$n++ ) {
            $item = $gameShow[$n];
            $totalWin += $item[3] ;
        }
        $res['view_html'].=$col1;
        $col1 ='<tr>';
        $col1.= '<td>开奖情况</td>';
        for ($n=0;$n < count($gameShow) ;$n++ )
        {
            $col1.= '<td><img src="../../gameImg/bairenyouxi/52001/lottery'.$imgArr[$gameShow[$n][0]].'.png" style="width: 70px;height: 70px;"></td>';
        }

        $col1.='</tr>';
        $res['view_html'].=$col1;

        $col1 ='<tr style="color: red">';
        $col1.= '<td>总萤分</td>';
        $col1.= '<td colspan="8">'.calculate($totalWin).'</td>';
        $col1.='</tr>';

        $res['view_html'].=$col1;
        return $res;
    }


    /**
     * 跑马
     * @param $res
     * @return array
     */
    public function  G53001($res)
    {

        //{"ResultTemp":null,"GameShow":[4,3],"BetArea":[1000,0,0,2000,1000,1000,1000,0,2000,1000,0],"PlayerReward":12000}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $posArr = [
            '1-6',
            '1-5',
            '1-4',
            '1-3',
            '1-2',
            '2-6',
            '2-5',
            '2-4',
            '2-3',
            '3-6',
            '3-5',
            '3-4',
            '4-6',
            '4-5',
            '5-6',
        ]   ;

        $count = count($posArr);
        $col1='<tr>';
        $col1 .= '<td>区域</td>';
        for ($n=0;$n < $count ;$n++ ) {
            $col1 .= '<td>' . ($posArr[$n]) . '</td>';
        }
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1 .= '<td>赔率</td>';
        for ($n=0;$n < $count ;$n++ ) {
            $col1 .= '<td>' . ($betObj->Odds[$n]) . '</td>';
        }
        $col1.='</tr>';

        $res['view_html'].=$col1;
        $col1 ='<tr style="color: green">';
        $col1 .= '<td>压住</td>';
        for ($n=0;$n < $count ;$n++ ) {
            $col1 .= '<td>' . calculate($betObj->BetArea[$n]) . '</td>';
        }
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1 .= "<td>萤分</td>";
        for ($n=0;$n < $count ;$n++ ) {
            $col1.= '<td>'.calculate($betObj->AreaResult*$betObj->Odds[$n]).'</td>';
        }
        $col1.='</tr>';
        $res['view_html'].=$col1;
        $col1 ='<tr>';
        $col1.= '<td>开奖情况</td>';
        $col1.= '<td>'.$posArr[$betObj->AreaResult].'</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        $col1 ='<tr style="color: red">';
        $col1.= '<td>总萤分</td>';
        $col1.= '<td colspan="5">'.calculate($betObj->BetArea[$betObj->AreaResult]*$betObj->Odds[$betObj->AreaResult]).'</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        return $res;
    }


    /**
     * 僵尸开奖结果
     * @param $res
     * @return array
     */
    public function  G30007($res)
    {

        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        for ($i=0;$i < 3 ;$i++ )
        {
            $col1 ='<tr>';
            $col1.='<td colspan="3">';
            $col1.='<div style="text-align: center; margin: 0 auto">';
            for ($n=0;$n < 5 ;$n++ )
            {
                $col1.= '<span><img src="../../gameImg/labaji/30007/ico_slots_jiangshi_'.$retObj[$i][$n].'.png" style="width: 40px;height: 40px;"></span>';
            }
            $col1.='</div>';
            $col1.='</td></tr>';
            $res['view_html'].=$col1;
        }

        $res['view_html'].= $this->getLineScoreList($betObj,30);
        return $res;
    }


    /**
     * 招财进宝开奖结果
     * @param $res
     * @return array
     */
    public function  G30008($res)
    {
        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        for ($i=0;$i < 3 ;$i++ )
        {
            $col1 ='<tr>';
            $col1.='<td colspan="3">';
            $col1.='<div style="text-align: center; margin: 0 auto">';
            for ($n=0;$n < 5 ;$n++ )
            {
                $col1.= '<span><img src="../../gameImg/labaji/30008/ico_slots_caishen_'.$retObj[$i][$n].'.png" style="width: 40px;height: 40px;"></span>';
            }
            $col1.='</div>';
            $col1.='</td></tr>';
            $res['view_html'].=$col1;
        }

        $res['view_html'].= $this->getLineScoreList($betObj,30);
        $list = $this->select(['gameid'=>$res['gameid'],'gameNumb'=>$res['gameNumb'],'playid'=>$res['playid']]);
        $list = arrlist_multisort($list,'id');
        unset($list[0]);
        //$betObj->WinReward/1000
        $col1='';
        $start=$betObj->WinReward;
        foreach ($list as $v){
            $result = xn_json_decode($v['result']);
            $col1 .='<tr style="color: blue">';
            $col1.= '<td>翻牌</td>';
            $col1.= '<td>'.($start/1000).'</td>';
            $col1.= '<td>'.($result['WinReward']==0 ? 0:$start*2/1000).'</td>';
            $col1.='</tr>';
            $start*=2;
        }

        $res['view_html'].=$col1;
        return $res;
    }


    /**
     * Croods开奖结果
     * @param $res
     * @return array
     */
    public function  G30005($res)
    {

        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        for ($i=0;$i < 3 ;$i++ )
        {
            $col1 ='<tr>';
            $col1.='<td colspan="3">';
            $col1.='<div style="text-align: center; margin: 0 auto">';
            for ($n=0;$n < 5 ;$n++ )
            {

                $col1.= '<span><img src="../../gameImg/labaji/30005/ico_slots_croods_'.$retObj[$i][$n].'.png" style="width: 40px;height: 40px;"></span>';
            }
            $col1.='</div>';
            $col1.='</td></tr>';
            $res['view_html'].=$col1;
        }

        $res['view_html'] .=$this->getLineScoreList($betObj,30);
        return $res;
    }
    /**
     * 金瓶梅开奖结果
     * @param $res
     * @return array
     */
    public function  G30006($res)
    {

        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        for ($i=0;$i < 3 ;$i++ )
        {
            $col1 ='<tr>';
            $col1.='<td colspan="3">';
            $col1.='<div style="text-align: center; margin: 0 auto">';
            for ($n=0;$n < 5 ;$n++ )
            {

                $col1.= '<span><img src="../../gameImg/labaji/30006/ico_slots_jinpingmei_'.$retObj[$i][$n].'.png" style="width: 60px;height: 60px;"></span>';
            }
            $col1.='</div>';
            $col1.='</td></tr>';
            $res['view_html'].=$col1;
        }

        $res['view_html'] .=$this->getLineScoreList($betObj,30);

        return $res;
    }


    /**
     * 大吉大利开奖结果
     * @param $res
     * @return array
     */
    public function  G32001($res)
    {

        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        for ($i=0;$i < 1 ;$i++ )
        {
            $col1 ='<tr>';
            $col1.='<td colspan="3">';
            $col1.='<div style="text-align: center; margin: 0 auto">';
            for ($n=0;$n < 3 ;$n++ )
            {

                $col1.= '<span><img src="../../gameImg/labaji/32001/ico_slots_ol1_'.$retObj[$i][$n].'.png" style="width: 40px;height: 40px;"></span>';
            }
            $col1.='</div>';
            $col1.='</td></tr>';
            $res['view_html'].=$col1;
        }

        $res['view_html'] .=$this->getLineScoreList($betObj,1);
        return $res;


        return $res;
    }


    /**
     * 财神到
     * @param $res
     */
    public function  G32004($res)
    {

        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        $res['view_html'] = '';
        $scoreHmtl = '';
        $imgMap = [
            '2'=>'GameView_Plist_GameView_Plist_13.png',
            '3'=>'GameView_Plist_GameView_Plist_30.png',
            '4'=>'GameView_Plist_GameView_Plist_16.png',
            '5'=>'GameView_Plist_GameView_Plist_27.png',
            '6'=>'GameView_Plist_GameView_Plist_23.png',
            '7'=>'GameView_Plist_GameView_Plist_26.png',
            '8'=>'GameView_Plist_GameView_Plist_25.png',
            '9'=>'GameView_Plist_GameView_Plist_29.png',
            '10'=>'GameView_Plist_GameView_Plist_10.png',
            '11'=>'GameView_Plist_GameView_Plist_37.png',
            '12'=>'GameView_Plist_GameView_Plist_24.png',
        ];
        $jiIndexs =[];
        //全同中奖

        for ($i=0;$i < 3 ;$i++ )
        {
            //第一列中有11 吉利的时候后面都必须得变成吉
            if($i==0)
            {

                for ($n=0;$n < 5 ;$n++ )
                {
                    if($retObj[$i][$n] ==11)
                    {
                        $jiIndexs[] = $n;
                    }
                }
            }

            $col1 ='<tr>';
            $col1.='<td colspan="3">';
            $col1.='<div style="text-align: center; margin: 0 auto">';
            //foreach ($retObj[$i] as $subItem)
            for ($ii=0;$ii < 5 ;$ii++ )
            {
                $imgIndex = $retObj[$i][$ii];
                if(in_array($ii,$jiIndexs))
                {
                    $imgIndex = 11;
                }
                $col1.= '<span><img src="../../gameImg/labaji/财神到/'.$imgMap[(string)$imgIndex].'" style="width: 30px;height: 30px;"></span>';
            }
            $col1.='</div>';
            $col1.='</td></tr>';
            $scoreHmtl.=$col1;
        }
        $res['view_html'] .=$scoreHmtl;
        $res['view_html'] .=$this->getSpecalLineScoreList($betObj,9);


        return  $res;
    }

    /**
     * 水浒传
     * @param $res
     */
    public function  G32006($res)
    {

        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        $res['view_html'] = '';
        $scoreHmtl = '';
        $imgMap = [
            '2'=>'2.png',
            '3'=>'3.png',
            '4'=>'4.png',
            '5'=>'5.png',
            '6'=>'6.png',
            '7'=>'7.png',
            '8'=>'8.png',
            '9'=>'9.png',
            '10'=>'10.png',
        ];

        $allItems = array_values($betObj->Result);
        $allItems = array_unique($allItems);
        //全同中奖
        for ($i=0;$i < 3 ;$i++ )
        {
            $col1 ='<tr>';
            $col1.='<td colspan="3">';
            $col1.='<div style="text-align: center; margin: 0 auto">';
            foreach ($retObj[$i] as $subItem)
            {
                $col1.= '<span><img src="../../gameImg/labaji/shuihu/'.$imgMap[(string)$subItem].'" style="width: 30px;height: 30px;"></span>';
            }
            $col1.='</div>';
            $col1.='</td></tr>';
            $scoreHmtl.=$col1;
        }
        $res['view_html'] .=$scoreHmtl;
        $res['view_html'] .=$this->getSpecalLineScoreList($betObj,9);


        return  $res;
    }

    /**
     * 获取牌型
     * @param $boards
     */
    private  function getNnBoardStyle($cards)
    {
        // 处理数据
        foreach ($cards as $key => $value) {
            if(!is_numeric($value)){
                $cards[$key] = 0;
            }
            $cards[$key] = (int)$value;
        }
        $r = $this->arrangement($cards, 3);
        foreach ($r as $key => $value) {
            if(0 == (array_sum($value) % 10)){
                # $res = array_diff($cards, $value);
                $res = array_sum($cards) - array_sum($value);
                $taurus = 0;
                $tmp = array_sum($res) % 10;
                if(0 == $tmp){
                    return "牛牛";
                }
                if(array_sum($res) % 10 > $taurus){
                    $taurus = $tmp;
                }
            }
        }
        if(isset($taurus)){
            return "牛$taurus";
        }
        return "没牛";

    }

   private  function arrangement($a, $m){
        $r = array();
        $n = count($a);
        if($m <= 0 || $m > $n){
            return $r;
        }
        for ($i=0; $i < $n; $i++) {
            $b = $a;
            $t = array_splice($b, $i, 1);
            if ($m == 1) {
                $r[] = $t;
            } else {
                $c = arrangement($b, $m - 1);
                foreach ($c as $v) {
                    $r[] = array_merge($t, $v);
                }
            }
        }
        return $r;
    }

    /**
     * 百人牛牛开奖结果
     * @param $res
     * @return array
     */
    public function  G70001($res)
    {
        //{"ResultTemp":null,"RoomType":70001,"BankerBoard":[13,39,51,21,1],"PlayerBoard":[[37,9,6,16,14],[36,7,3,29,17],[12,10,38,49,4],[44,45,27,50,11]],"RewardArea":[0,0,0,0,0],"BetArea":[0,0,0,0,0],"PlayerReward":0}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $bankObj =  $betObj->BankerBoard;
        $playerObj =  $betObj->PlayerBoard;
        $rewardArea =   $betObj->RewardArea;
        $totalBet = array_sum($betObj->BetArea) ;
        $totalWin = array_sum($betObj->RewardArea) ;


        /*
        ENiuniu_Type_Meiniu = 0; 没牛

        ENiuniu_Type_Niu1 = 1; 牛一

       ENiuniu_Type_Niu2=2;

        ENiuniu_Type_Niu3=3;

        ENiuniu_Type_Niu4=4;

        ENiuniu_Type_Niu5=5;

        ENiuniu_Type_Niu6=6;

        ENiuniu_Type_Niu7=7;

        ENiuniu_Type_Niu8=8;

        ENiuniu_Type_Niu9=9;

        ENiuniu_Type_Niuniu=10;

        ENiuniu_Type_Shunzi=11;//顺子牛

        ENiuniu_Type_WuHua=12;//无花牛

        ENiuniu_Type_Tonghua=13;//同花牛

        ENiuniu_Type_Hulu=14;//葫芦牛

        ENiuniu_Type_Zhadan=15;//炸弹牛

        ENiuniu_Type_Wuxiaoniu=16;//五小牛
        */

        $cardTypeMap = [
            '0'=>'没牛',
            '1'=>'牛1',
            '2'=>'牛2',
            '3'=>'牛3',
            '4'=>'牛4',
            '5'=>'牛5',
            '6'=>'牛6',
            '7'=>'牛7',
            '8'=>'牛8',
            '9'=>'牛9',
            '10'=>'牛牛',
            '11'=>'顺子牛',
            '12'=>'无花牛',
            '13'=>'同花牛',
            '14'=>'葫芦牛',
            '15'=>'炸弹牛',
            '16'=>'五小牛',
        ];



        $col1 ='<tr>';
        $col1.= '<td>庄家牌</td>';
        $col1.= '<td>';
        for ($n=0;$n < 5 ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker/POKER'.$playerObj[0][$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';

        $col1 .='<tr>';
        $col1.= '<td>庄-牌型</td>';
        $col1.= '<td><span class="badge bg-red">'.$cardTypeMap[$betObj->CardType[0]].'</span></td>';
        $col1.= '<td ></td>';
        $col1.= '<td></td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>东</td>';
        $col1.= '<td colspan="3">';
        for ($n=0;$n < 5 ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker/POKER'.$playerObj[1][$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';
        $col1.='</tr>';

        $col1.= '<td>东-牌型</td>';
        $col1.= '<td >';
        $col1.= '<span class="badge bg-red">'.$cardTypeMap[$betObj->CardType[1]].'</span>';
        $col1.= ' 压住:'.'<span class="badge bg-green">'.calculate($betObj->BetArea[1]).'</span>';
        $col1.= ' 萤分:<span class="badge bg-red">'.(calculate($rewardArea[1])>0?calculate($rewardArea[1]):'0').'</span>';
        $col1.='</td>';
        $col1.='</tr>';


        $col1 .='<tr>';
        $col1.= '<td>南</td>';
        $col1.= '<td colspan="3">';
        for ($n=0;$n < 5 ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker/POKER'.$playerObj[2][$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';

        $col1.='</tr>';
        $col1.= '<td>南-牌型</td>';
        $col1.= '<td>';
        $col1.= '<span class="badge bg-red">'.$cardTypeMap[$betObj->CardType[2]].'</span>';
        $col1.= ' 压住:'.'<span class="badge bg-green">'.calculate($betObj->BetArea[2]).'</span>';
        $col1.=  ' 萤分:<span class="badge bg-red">'.(calculate($rewardArea[2])>0?calculate($rewardArea[2]):'0').'</span>';
        $col1.= '</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>西</td>';
        $col1.= '<td colspan="3">';
        for ($n=0;$n < 5 ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker/POKER'.$playerObj[3][$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';
        $col1.='</tr>';

        $col1.= '<td>西-牌型</td>';
        $col1.= '<td>';
        $col1.= '<span class="badge bg-red">'.$cardTypeMap[$betObj->CardType[3]].'</span>';
        $col1.= ' 压住:'.'<span class="badge bg-green">'.calculate($betObj->BetArea[3]).'</span>';
        $col1.=  ' 萤分:<span class="badge bg-red">'.(calculate($rewardArea[3])>0?calculate($rewardArea[3]):'0').'</span>';
        $col1 .='</td>';
        $col1.='</tr>';


        $col1 .='<tr>';
        $col1.= '<td>北</td>';
        $col1.= '<td colspan="3">';
        for ($n=0;$n < 5 ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker/POKER'.$playerObj[4][$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';
        $col1.='</tr>';

        $col1.= '<td>北-牌型</td>';
        $col1.= '<td>';
        $col1.= '<span class="badge bg-red">'.$cardTypeMap[$betObj->CardType[4]].'</span>';
        $col1.= ' 压住:'.'<span class="badge bg-green">'.calculate($betObj->BetArea[4]).'</span>';
        $col1.=  ' 萤分:<span class="badge bg-red">'.(calculate($rewardArea[4])>0?calculate($rewardArea[4]):'0').'</span>';
        $col1.='</td></tr>';

        $col1 .='<tr>';
        $col1.= '<td>合计</td>';
        $col1.= '<td colspan="3">';
        $col1.= ' 总压住:'.'<span class="badge bg-green">'.calculate($totalBet).'</span>';
        $col1.=  '萤分:<span class="badge bg-red">'.calculate($totalBet+$totalWin).'</span>';
        $col1.= '</td>';
        $col1.='</tr>';

        $res['view_html'].=$col1;
        return $res;
    }

    /**
     * 百家乐开奖结果
     * @param $res
     * @return array
     */
    public function  G10001($res)
    {
        //{"ResultTemp":null,"RoomType":0,"BankerBoard":[25,13,35],"PlayerBoard":[44,5,14],"RewardArea":[5,1],"BetArea":[0,1000,0,0,1000],"PlayerReward":12000}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $bankObj =  $betObj->BankerBoard;
        $playerObj =  $betObj->PlayerBoard;;
        $col1 ='<tr>';
        $col1.= '<td>闲家牌</td>';
        $col1.= '<td>';
        for ($n=0;$n < count($playerObj) ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker/POKER'.$playerObj[$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>庄家牌</td>';
        $col1.= '<td>';
        for ($n=0;$n < count($bankObj) ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker/POKER'.$bankObj[$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';
        $col1.='</tr>';

        $col1.='<tr>';
        $col1.= '<td>闲家压住</td>';
        $col1.= '<td>';
        $col1.=($betObj->BetArea[1]/1000)>0?($betObj->BetArea[1]/1000):'';
        $col1.='</td>';
        $col1.='</tr>';

        $col1.='<tr>';
        $col1.= '<td>庄家压住</td>';
        $col1.= '<td>';
        $col1.=($betObj->BetArea[0]/1000)>0?($betObj->BetArea[0]/1000):'';
        $col1.='</td>';
        $col1.='</tr>';

        $col1.='<tr>';
        $col1.= '<td>和压住</td>';
        $col1.= '<td>';
        $col1.=($betObj->BetArea[2]/1000)>0?($betObj->BetArea[2]/1000):'';
        $col1.='</td>';
        $col1.='</tr>';

        $col1.='<tr>';
        $col1.= '<td>闲家对子</td>';
        $col1.= '<td>';
        $col1.=($betObj->BetArea[4]/1000)>0?($betObj->BetArea[4]/1000):'';
        $col1.='</td>';
        $col1.='</tr>';

        $col1.='<tr>';
        $col1.= '<td>庄家对子</td>';
        $col1.= '<td>';
        $col1.=($betObj->BetArea[3]/1000)>0?($betObj->BetArea[3]/1000):'';
        $col1.='</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        return $res;
    }


    /**
     * 飞禽走兽开奖结果
     * @param $res
     * @return array
     */
    public function  G200006($res) //没下注也写
    {
        //{"ResultTemp":null,"GameShow":[4,3],"BetArea":[1000,0,0,2000,1000,1000,1000,0,2000,1000,0],"PlayerReward":12000}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $fqArr =  [2,1, 0,6,7];

        $col1='<tr>';
        $col1 .= '<td>飞禽</td>';
        for ($n=0;$n < 5 ;$n++ ) {
            $col1.= '<td><img src="../../gameImg/bairenyouxi/200006/Animal_'.$fqArr[$n].'.png?id='.time().'" style="width: 40px;height: 40px;"></td>';
        }
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1 .= '<td>赔率</td>';
        for ($n=0;$n < 5 ;$n++ ) {
            $col1 .= '<td>' . ($betObj->Odds[$fqArr[$n]]) . '</td>';
        }
        $col1.='</tr>';

        $res['view_html'].=$col1;
        $col1 ='<tr style="color: green">';
        $col1 .= '<td>压住</td>';
        for ($n=0;$n < 5 ;$n++ ) {
            $col1 .= '<td>' . calculate($betObj->BetArea[$fqArr[$n]]) . '</td>';
        }
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1 .= '<td>萤分</td>';
        for ($n=0;$n < 5 ;$n++ ) {
            $col1.= '<td>'.$this->getZsWinByIndex($fqArr[$n] ,$betObj->GameShow,$betObj->BetArea,$betObj->Odds[$fqArr[$n]]).'</td>';
        }
        $col1.='</tr>';


        $res['view_html'].=$col1;

        $zsArr =  [3,4,5,9,10];
        $col1='<tr>';
        $col1 .= '<td>走兽</td>';
        for ($n=0;$n < 5 ;$n++ ) {
            $col1.= '<td><img src="../../gameImg/bairenyouxi/200006/Animal_'.$zsArr[$n].'.png?id='.time().'" style="width: 40px;height: 40px;"></td>';
        }
        $col1 .='</tr>';

        $col1 .='<tr>';
        $col1 .= '<td>赔率</td>';
        for ($n=0;$n < 5 ;$n++ ) {
            $col1 .= '<td>' . ($betObj->Odds[$zsArr[$n]]) . '</td>';
        }
        $col1.='</tr>';

        $res['view_html'].=$col1;

        $col1 ='<tr>';
        $col1 .='<tr style="color: green">';
        $col1 .= '<td>压住</td>';
        for ($n=0;$n < 5 ;$n++ ) {
            $col1 .= '<td>' . calculate($betObj->BetArea[$zsArr[$n]]) . '</td>';
        }
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1 .= '<td>萤分</td>';
        for ($n=0;$n < 5 ;$n++ ) {
            $col1.= '<td>'.$this->getZsWinByIndex($zsArr[$n] ,$betObj->GameShow,$betObj->BetArea,$betObj->Odds[$zsArr[$n]]).'</td>';
        }
        $col1.='</tr>';

        $res['view_html'].=$col1;

        $col1 ='<tr>';
        $col1.= '<td>金沙银沙</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200006/Animal_8.png" style="width: 40px;height: 40px;"></td>';
        $jsArr =  [8];
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1 .='<tr >';
        $col1 .= '<td>赔率</td>';
        $col1.='<td>';

        //判断出金沙
        $openJs = count($betObj->GameShow)==3;
        if($openJs|| $betObj->GameShow[0]==-100)
        {
            $col1 .=  $betObj->Odds[$jsArr[0]];
        }else{
            $col1 .= 0;
        }
		//$col1 .=  $betObj->Odds[$jsArr[0]];

        $col1.='</td></tr>';

        $col1 .='<tr style="color: green">';
        $col1 .= '<td>压住</td>';
        $col1 .= '<td>'. calculate($betObj->BetArea[$jsArr[0]]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1 .= '<td>萤分</td>';
        $col1.='<td>';
		$col1.= $this->getZsWinByIndex($jsArr[0] ,$betObj->GameShow,$betObj->BetArea,$betObj->Odds[$jsArr[0]]).'</td>';
		$col1.='</td></tr>';
        $res['view_html'].=$col1;
        $col1 ='<tr>';
        $col1.= '<td>开奖情况</td>';
        $gameShow =  $betObj->GameShow;
        for ($n=0;$n < count($gameShow) ;$n++ )
        {
            $col1.= '<td><img src="../../gameImg/bairenyouxi/200006/Animal_'.$gameShow[$n].'.png" style="width: 70px;height: 70px;"></td>';
        }

        if($openJs)
        {
            $col1.= '<td colspan="3"></td>';
        }else{

            $col1.= '<td colspan="4"></td>';
        }
        $col1.='</tr>';
        $res['view_html'].=$col1;

        $col1 ='<tr style="color: red">';
        $col1.= '<td>总萤分</td>';
        $col1.= '<td colspan="5">'.calculate($betObj->PlayerReward).'</td>';
        $col1.='</tr>';

        $res['view_html'].=$col1;
        return $res;
    }

    /**
     * 红黑大战开奖结果
     * @param $res
     * @return array
     */
    public function  G80001($res)
    {
        //{"ResultTemp":null,"RoomType":80001,"RedBoard":null,"BlackBoard":[40,33,2],"RewardArea":[0,0,-61000],"BetArea":[0,0,61000],"PlayerReward":-61000}

        $res['view_html'] = '';
        $betObj = $res['result'];

        $col1 ='<tr>';
        $col1.= '<td>红牌</td>';
        $col1.= '<td>';
        for ($n=0;$n < 3 ;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker/POKER'.$betObj->RedBoard[$n].'.png" style="width: 40px;height: 40px;"></span>';
        }
        $col1.= '</td>';
        $col1.='</tr>';
        $col1 .='<tr>';
        $col1.= '<td>下注</td>';
        $col1.= '<td>'.calculate($betObj->BetArea[2]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>黑牌</td>';
        $col1.= '<td>';
        for ($n=0;$n < 3;$n++ )
        {
            $col1.= '<span><img src="../../gameImg/poker/POKER'.$betObj->BlackBoard[$n].'.png" style="width: 40px;height: 40px;"></>';
        }
        $col1.= '</td>';
        $col1.='</tr>';
        $col1 .='<tr>';
        $col1.= '<td>下注</td>';
        $col1.= '<td>'.calculate($betObj->BetArea[1]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>幸运一击下注</td>';
        $col1.= '<td>'.calculate($betObj->BetArea[0]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>萤分</td>';
        $col1.= '<td colspan="3">'.calculate($betObj->PlayerReward).'</td>';
        $col1.='</tr>';

        $res['view_html'].=$col1;

        return $res;
    }
    /**
     * 龙虎大战开奖结果
     * @param $res
     * @return array
     */
    public function  G680001($res)
    {
        //{"ResultTemp":null,"RoomType":680001,"LongBoard":[52],"HuBoard":[45],"RewardArea":[0,0,-50000],"BetArea":[0,0,50000],"PlayerReward":-50000}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $col1 ='<tr style="text-align: center;background-color: #2b2b2b;color: White">';
        $col1.= '<td>压住</td>';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>龙牌</td>';
        $col1.= '<td>虎牌</td>';
        $col1.= '<td>龙压住</td>';
        $col1.= '<td>虎压住</td>';
        $col1.= '<td>和压住</td>';
        $col1.='</tr>';
        $col1 .='<tr>';
        $col1.= '<td>'.(calculate($betObj->BetArea[0]+$betObj->BetArea[1]+$betObj->BetArea[2])).'</td>';
        $col1.= '<td>'.(calculate($betObj->PlayerReward)).'</td>';
        $col1.= '<td><span><img src="../../gameImg/poker/POKER'.($betObj->LongBoard[0]).'.png" style="width: 40px;height: 40px;"></span></td>';
        $col1.= '<td><span><img src="../../gameImg/poker/POKER'.($betObj->HuBoard[0]).'.png" style="width: 40px;height: 40px;"></span></td>';
        $col1.= '<td>'.calculate($betObj->BetArea[1]).'</td>';
        $col1.= '<td>'.calculate($betObj->BetArea[2]).'</td>';
        $col1.= '<td>'.calculate($betObj->BetArea[0]).'</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;

        return $res;
    }


    /**
     * 获取萤分
     * @param $index
     * @param $resultArr
     * @param $betArr
     * @param $peilv
     * @return int
     */
    private function getWinByIndex($index ,$resultArr,$betArr,$peilv)    {
        //开奖位置
        $resultIndex =[];
        foreach($resultArr as $item)
        {
            $resultIndex[] = $item[0];
        }

        $win =  0;
            foreach ($resultIndex as $item)
            {
                if($item ==  $index)
                {
                    $win = $betArr[$index]*$peilv/1000;
                }
            }

            return $win;
    }

    /**
     * 飞禽走兽位置萤分
     * @param $index
     * @param $resultArr
     * @param $betArr
     * @param $peilv
     * @return int
     */
    private function getZsWinByIndex($index ,$resultArr,$betArr,$peilv)    {
        //开奖位置
        $resultIndex =[];
        foreach($resultArr as $item)
        {
            $resultIndex[] = $item;
        }
        if(count($resultArr)==1)
        {
            if($resultArr[0]==-100)
            {
                $isTongpei = true;
            }
        }
        $win =  0;
        foreach ($resultIndex as $item)
        {
            if($isTongpei)
            {
                $win = $betArr[$index]*$peilv/1000;
            }
            else{
                if($item ==  $index)
                {
                    $win = $betArr[$index]*$peilv/1000;
                }
            }
        }

        return $win;
    }


    /**
     * 获取牛仔位置萤分
     * @param $index
     * @param $resultArr
     * @param $betArr
     * @param $peilv
     * @return int
     */
    private function getDZNZWinByIndex($index ,$resultArr,$betArr,$peilv)    {
        //开奖位置
        $resultIndex =[];
        foreach($resultArr as $item)
        {
            $resultIndex[] = $item;
        }

        $win =  0;
        foreach ($resultIndex as $item)
        {
            if($item ==  $index)
            {
                $win = $betArr[$index]*$peilv/1000;
            }
        }

        return $win;
    }


    /**
     * 极速时刻开奖结果
     * @param $res
     * @return array
     */
    public function  G200002($res) //没下注也写
    {
        //{"ResultTemp":null,"AreaResult":[[3,1],[4,1]],"BetArea":[0,3000,0,2000,0,3000,2000,0,0,0,2000,2000,2000,2000,0],"PlayerReward":10000,"Odds":[28,14,8,5,2,35,17,11,7,8,40,20,13,8,2]}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $col1 ='<tr>';
        $res['view_html'].= $col1;
        $countResult = count($betObj->AreaResult);

        $scoreHtrml = '';
        $scoreHtrml .= '<td><div style="margin: 0 auto; width: 100px">';
        //小三元/大四喜
        for($i = 0 ; $i < $countResult;$i++)
        {
            if($betObj->AreaResult[$i][0] == 100  )
            {
                $scoreHtrml.= '<img src="../../gameImg/bairenyouxi/200002/tongsha.png" style="width: 60px;height: 60px;">';
            }
            else if($betObj->AreaResult[$i][0] == -100  )
            {
                $scoreHtrml.= '<img src="../../gameImg/bairenyouxi/200002/通赔.png" style="width: 60px;height: 60px;">';
            }
            else{
                $scoreHtrml.= '<img src="../../gameImg/bairenyouxi/200002/'.$betObj->AreaResult[$i][0].'.png" style="width: 60px;height: 60px;">';
            }
        }
        $scoreHtrml .= '</div></td>';
        $col1 .='</tr>';
        $res['view_html'].= $scoreHtrml;
        //红色
        $col1 .='<tr style="background: red;color: white">';
        $col1.= '<td>信息</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/0.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/1.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/2.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/3.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/4.png" style="width: 60px;height: 60px;"></td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>赔率</td>';
        $col1.= '<td>'.($betObj->Odds[0]).'</td>';
        $col1.= '<td>'.($betObj->Odds[1]).'</td>';
        $col1.= '<td>'.($betObj->Odds[2]).'</td>';
        $col1.= '<td>'.($betObj->Odds[3]).'</td>';
        $col1.= '<td>'.($betObj->Odds[4]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>'.($betObj->BetArea[0]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[1]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[2]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[3]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[4]/1000).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>萤分</td>';

        $col1.= '<td>'.$this->getWinByIndex(0 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[0]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(1 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[1]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(2 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[2]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(3 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[3]).'</td>';
       $col1.= '<td>'.$this->getWinByIndex(4 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[4]).'</td>';


        $col1.='</tr>';
        //绿色
        $col1 .='<tr style="background: green;color: white">';
        $col1.= '<td>信息</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/5.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/6.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/7.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/8.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/9.png" style="width: 60px;height: 60px;"></td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>赔率</td>';
        $col1.= '<td>'.($betObj->Odds[5]).'</td>';
        $col1.= '<td>'.($betObj->Odds[6]).'</td>';
        $col1.= '<td>'.($betObj->Odds[7]).'</td>';
        $col1.= '<td>'.($betObj->Odds[8]).'</td>';
        $col1.= '<td>'.($betObj->Odds[9]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>'.($betObj->BetArea[5]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[6]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[7]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[8]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[9]/1000).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>'.$this->getWinByIndex(5 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[5]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(6 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[6]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(7 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[7]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(8 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[8]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(9 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[9]).'</td>';
        $col1 .='</tr>';



        //黄色

        $col1 .='<tr style="background: #c77405;color: white">';
        $col1.= '<td>信息</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/10.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/11.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/12.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/13.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200002/14.png" style="width: 60px;height: 60px;"></td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>赔率</td>';
        $col1.= '<td>'.($betObj->Odds[10]).'</td>';
        $col1.= '<td>'.($betObj->Odds[11]).'</td>';
        $col1.= '<td>'.($betObj->Odds[12]).'</td>';
        $col1.= '<td>'.($betObj->Odds[13]).'</td>';
        $col1.= '<td>'.($betObj->Odds[14]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>'.($betObj->BetArea[10]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[11]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[12]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[13]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[14]/1000).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>'.$this->getWinByIndex(10 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[10]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(11 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[11]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(12 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[12]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(13 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[13]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(14 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[14]).'</td>';
        $col1.='</tr>';

        $res['view_html'].=$col1;
        return $res;
    }

    /**
     * 疯狂水果开奖结果
     * @param $res
     * @return array
     */
    public function  G200001($res)
    {

        //{"ResultTemp":null,"AreaResult":[[3,1],[4,1]],"BetArea":[0,3000,0,2000,0,3000,2000,0,0,0,2000,2000,2000,2000,0],"PlayerReward":10000,"Odds":[28,14,8,5,2,35,17,11,7,8,40,20,13,8,2]}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $col1 ='<tr>';
        $res['view_html'].= $col1;
        $countResult = count($betObj->AreaResult);
        $scoreHtrml = '';
        $scoreHtrml .= '<td><div style="margin: 0 auto; width: 100px">';
        //小三元/大四喜
        for($i = 0 ; $i < $countResult;$i++)
        {
            if($betObj->AreaResult[$i][0] == 100  )
            {
                $scoreHtrml.= '<img src="../../gameImg/bairenyouxi/200001/tongsha.png" style="width: 60px;height: 60px;">';
            }
            else if($betObj->AreaResult[$i][0] == -100  )
            {
                $scoreHtrml.= '<img src="../../gameImg/bairenyouxi/200001/tongpei.png" style="width: 60px;height: 60px;">';
            }
            else{
                $scoreHtrml.= '<img src="../../gameImg/bairenyouxi/200001/'.$betObj->AreaResult[$i][0].'.png" style="width: 60px;height: 60px;">';
            }
        }
        $scoreHtrml .= '</div></td>';
        $col1 .='</tr>';
        $res['view_html'].= $scoreHtrml;
        //红色
        $col1 .='<tr style="background: red;color: white">';
        $col1.= '<td>信息</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/0.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/1.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/2.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/3.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/4.png" style="width: 60px;height: 60px;"></td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: green">';
        $col1.= '<td>赔率</td>';
        $col1.= '<td>'.($betObj->Odds[0]).'</td>';
        $col1.= '<td>'.($betObj->Odds[1]).'</td>';
        $col1.= '<td>'.($betObj->Odds[2]).'</td>';
        $col1.= '<td>'.($betObj->Odds[3]).'</td>';
        $col1.= '<td>'.($betObj->Odds[4]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>'.($betObj->BetArea[0]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[1]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[2]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[3]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[4]/1000).'</td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>'.$this->getWinByIndex(0 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[0]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(1 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[1]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(2 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[2]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(3 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[3]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(4 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[4]).'</td>';
        $col1.='</tr>';
        //绿色
        $col1 .='<tr style="background: green;color: white">';
        $col1.= '<td>信息</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/5.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/6.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/7.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/8.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/9.png" style="width: 60px;height: 60px;"></td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: green">';
        $col1.= '<td>赔率</td>';
        $col1.= '<td>'.($betObj->Odds[5]).'</td>';
        $col1.= '<td>'.($betObj->Odds[6]).'</td>';
        $col1.= '<td>'.($betObj->Odds[7]).'</td>';
        $col1.= '<td>'.($betObj->Odds[8]).'</td>';
        $col1.= '<td>'.($betObj->Odds[9]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>'.($betObj->BetArea[5]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[6]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[7]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[8]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[9]/1000).'</td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>'.$this->getWinByIndex(5 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[5]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(6 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[6]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(7 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[7]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(8 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[8]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(9 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[9]).'</td>';
        $col1 .='</tr>';



        //黄色

        $col1 .='<tr style="background: #c77405;color: white">';
        $col1.= '<td>信息</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/10.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/11.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/12.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/13.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200001/14.png" style="width: 60px;height: 60px;"></td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: green">';
        $col1.= '<td>赔率</td>';
        $col1.= '<td>'.($betObj->Odds[10]).'</td>';
        $col1.= '<td>'.($betObj->Odds[11]).'</td>';
        $col1.= '<td>'.($betObj->Odds[12]).'</td>';
        $col1.= '<td>'.($betObj->Odds[13]).'</td>';
        $col1.= '<td>'.($betObj->Odds[14]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>'.($betObj->BetArea[10]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[11]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[12]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[13]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[14]/1000).'</td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>'.$this->getWinByIndex(10 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[10]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(11 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[11]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(12 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[12]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(13 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[13]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(14 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[14]).'</td>';
        $col1.='</tr>';

        $res['view_html'].=$col1;
        return $res;

    }

    /**
     * 魔法宝石开奖结果
     * @param $res
     * @return array
     */
    public function  G200003($res)
    {
        //{"ResultTemp":null,"AreaResult":[[3,1],[4,1]],"BetArea":[0,3000,0,2000,0,3000,2000,0,0,0,2000,2000,2000,2000,0],"PlayerReward":10000,"Odds":[28,14,8,5,2,35,17,11,7,8,40,20,13,8,2]}
        $res['view_html'] = '';
        $betObj = $res['result'];
        $col1 ='<tr>';
        $res['view_html'].= $col1;
        $countResult = count($betObj->AreaResult);
        $scoreHtrml = '';
        $scoreHtrml .= '<td><div style="margin: 0 auto; width: 100px">';
        //小三元/大四喜
        for($i = 0 ; $i < $countResult;$i++)
        {
            if($betObj->AreaResult[$i][0] == 100  )
            {
                $scoreHtrml.= '<img src="../../gameImg/bairenyouxi/200003/tongsha.png" style="width: 60px;height: 60px;">';
            }
            else if($betObj->AreaResult[$i][0] == -100  )
            {
                $scoreHtrml.= '<img src="../../gameImg/bairenyouxi/200003/tongpei.png" style="width: 60px;height: 60px;">';
            }
            else{
                $scoreHtrml.= '<img src="../../gameImg/bairenyouxi/200003/'.$betObj->AreaResult[$i][0].'.png" style="width: 60px;height: 60px;">';
            }
        }
        $scoreHtrml .= '</div></td>';
        $col1 .='</tr>';
        $res['view_html'].= $scoreHtrml;
        //红色
        $col1 .='<tr style="background: red;color: white">';
        $col1.= '<td>信息</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/0.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/1.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/2.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/3.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/4.png" style="width: 60px;height: 60px;"></td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: green">';
        $col1.= '<td>赔率</td>';
        $col1.= '<td>'.($betObj->Odds[0]).'</td>';
        $col1.= '<td>'.($betObj->Odds[1]).'</td>';
        $col1.= '<td>'.($betObj->Odds[2]).'</td>';
        $col1.= '<td>'.($betObj->Odds[3]).'</td>';
        $col1.= '<td>'.($betObj->Odds[4]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>'.($betObj->BetArea[0]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[1]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[2]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[3]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[4]/1000).'</td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>'.$this->getWinByIndex(0 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[0]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(1 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[1]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(2 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[2]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(3 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[3]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(4 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[4]).'</td>';
        $col1.='</tr>';
        //绿色
        $col1 .='<tr style="background: green;color: white">';
        $col1.= '<td>信息</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/5.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/6.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/7.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/8.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/9.png" style="width: 60px;height: 60px;"></td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: green">';
        $col1.= '<td>赔率</td>';
        $col1.= '<td>'.($betObj->Odds[5]).'</td>';
        $col1.= '<td>'.($betObj->Odds[6]).'</td>';
        $col1.= '<td>'.($betObj->Odds[7]).'</td>';
        $col1.= '<td>'.($betObj->Odds[8]).'</td>';
        $col1.= '<td>'.($betObj->Odds[9]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>'.($betObj->BetArea[5]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[6]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[7]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[8]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[9]/1000).'</td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>'.$this->getWinByIndex(5 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[5]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(6 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[6]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(7 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[7]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(8 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[8]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(9 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[9]).'</td>';
        $col1 .='</tr>';

        //黄色
        $col1 .='<tr style="background: #c77405;color: white">';
        $col1.= '<td>信息</td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/10.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/11.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/12.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/13.png" style="width: 60px;height: 60px;"></td>';
        $col1.= '<td><img src="../../gameImg/bairenyouxi/200003/14.png" style="width: 60px;height: 60px;"></td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: green">';
        $col1.= '<td>赔率</td>';
        $col1.= '<td>'.($betObj->Odds[10]).'</td>';
        $col1.= '<td>'.($betObj->Odds[11]).'</td>';
        $col1.= '<td>'.($betObj->Odds[12]).'</td>';
        $col1.= '<td>'.($betObj->Odds[13]).'</td>';
        $col1.= '<td>'.($betObj->Odds[14]).'</td>';
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>'.($betObj->BetArea[10]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[11]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[12]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[13]/1000).'</td>';
        $col1.= '<td>'.($betObj->BetArea[14]/1000).'</td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: red">';
        $col1.= '<td>萤分</td>';
        $col1.= '<td>'.$this->getWinByIndex(10 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[10]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(11 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[11]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(12 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[12]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(13 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[13]).'</td>';
        $col1.= '<td>'.$this->getWinByIndex(14 ,$betObj->AreaResult,$betObj->BetArea,$betObj->Odds[14]).'</td>';
        $col1.='</tr>';

        $res['view_html'].=$col1;
        return $res;
    }


    /**
     * 舞狮争霸
     * @param $res
     * @return array
     */
    public function  G32005($res)
    {
        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        $num = count($retObj);
        $col1 ='<tr>';
        $col1 .='<td colspan="3">';
        $col1.='<div style="text-align: center; margin: 0 auto">';
        for ($i=0;$i < $num ;$i++ )
        {
            $col1 .='<tr>';
            $col1 .='<td colspan="3">';
            foreach ($retObj[$i] as $subItem)
            {
                $col1.= '<span><img src="../../gameImg/labaji/32005/'.$subItem.'.png" style="width: 40px;height: 40px;"></span>';
            }
            $col1 .='</td>';
            $col1 .='</tr>';

        }
        $col1.='</div>';
        $col1.='</td>';
        $col1 .='</tr>';

        $res['view_html'].=$col1;

        $col1 ='<tr  style="text-align: center;background-color: #2b2b2b;color: White">';
        $col1.= '<td>线</td>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>萤分</td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: green">';
        $col1.= '<td>游戏压住</td>';
        $col1.= '<td>'.($betObj->Bet/1000).'</td>';
        $col1.= '<td>'.($betObj->WinReward/1000).'</td>';
        $col1 .='</tr>';
        $col1 .='<tr style="color: red">';
        $col1.= '<td>所有总计</td>';
        $col1.= '<td>'.($betObj->Bet/1000).'</td>';
        $col1.= '<td>'.($betObj->WinReward/1000).'</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        return $res;
    }

    /**
     * 五龙争霸
     * @param $res
     * @return array
     */
    public function  G32007($res)
    {
        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        $num = count($retObj);
        $col1 ='<tr>';
        $col1 .='<td colspan="3">';
        $col1.='<div style="text-align: center; margin: 0 auto">';
        for ($i=0;$i < $num ;$i++ )
        {
            $col1 .='<tr>';
            $col1 .='<td colspan="3">';
            foreach ($retObj[$i] as $subItem)
            {
                $col1.= '<span><img src="../../gameImg/labaji/32007/'.$subItem.'.png" style="width: 40px;height: 40px;"></span>';
            }
            $col1 .='</td>';
            $col1 .='</tr>';

        }
        $col1.='</div>';
        $col1.='</td>';
        $col1 .='</tr>';

        $res['view_html'].=$col1;

        $col1 ='<tr  style="text-align: center;background-color: #2b2b2b;color: White">';
        $col1.= '<td>线</td>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>萤分</td>';
        $col1.='</tr>';

        $col1 .='<tr style="color: green">';
        $col1.= '<td>游戏压住</td>';
        $col1.= '<td>'.($betObj->Bet/1000).'</td>';
        $col1.= '<td>'.($betObj->WinReward/1000).'</td>';
        $col1 .='</tr>';
        $col1 .='<tr style="color: red">';
        $col1.= '<td>所有总计</td>';
        $col1.= '<td>'.($betObj->Bet/1000).'</td>';
        $col1.= '<td>'.($betObj->WinReward/1000).'</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        return $res;
    }


    /**
     * 连环夺宝
     * @param $res
     * @return array
     */
    public function  G51001($res)
    {
        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        $num = count($retObj);

        $col1 ='<tr  style="text-align: center;background-color: #2b2b2b;color: White">';
        $col1.= '<td>宝石</td>';
        $col1.= '<td>连续个数</td>';
        $col1.= '<td>萤分</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        $WinReward = 0;
        for ($i=0;$i < $num ;$i++ ) {
            $col1 = '<tr>';
            $col1 .= '<td> ';
            $subItem = $retObj[$i];
            $col1 .= '<tr>';
            $col1 .= '<td><span><img src="../../gameImg/bairenyouxi/51001/' . $subItem[0] . '.png" style="width: 40px;height: 40px;"></span></td>';
            $col1 .= '<td>' . $subItem[1] . '</td>';
            $col1 .= '<td>' . calculate($subItem[2] ) . '</td>';
            $col1 .= '</tr>';
            $res['view_html'].=$col1;
            $WinReward+=$subItem[2];
        }
        $col1 ='<tr style="color: red">';
        $col1.= '<td>所有总计</td>';
        $col1.= '<td>'.($betObj->Bet/1000).'</td>';
        $col1.= '<td>'.($WinReward/1000).'</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        return $res;
    }

    /**
     * 黑杰克开奖结果
     * @param $res
     * @return array
     */
    public function  getHjkDetail($res)
    {
        //{"ResultTemp":null,"RoomType":20001,"BankerBoard":[6,21,12],"PlayerBoard":null,"Bet":20000,"PlayerReward":40000}
        $betObj = $res['result'];
        $res['view_html'] = '';
        $col1 ='<tr>';
        $col1.= '<td>庄家牌</td>';
        for ($n=0;$n < count($betObj->BankerBoard) ;$n++ )
        {
            $col1.= '<td><img src="../../gameImg/poker/POKER'.$betObj->BankerBoard[$n].'.png" style="width: 60px;height: 60px;"></td>';
        }
        $col1.='</tr>';
        $res['view_html'].=$col1;
        $col1 ='<tr>';
        $col1.= '<td>玩家牌</td>';
        for ($n=0;$n < count($betObj->PlayerBoard) ;$n++ )
        {
            $col1.= '<td><img src="../../gameImg/poker/POKER'.$betObj->PlayerBoard[$n].'.png" style="width: 60px;height: 60px;"></td>';
        }

        $col1.='</tr>';
        $col1 .='<tr style="color: green">';
        $col1.= '<td>压住</td>';
        $col1.= '<td colspan="2">'.($betObj->Bet/1000).'</td>';
        $col1.='</tr>';

        $col1.='</tr>';
        $col1 .='<tr style="color: red">';
        $col1.= '<td>萤分</td>';
        $col1.= '<td colspan="2">'.($betObj->PlayerReward/1000).'</td>';
        $col1.='</tr>';

        $res['view_html'].=$col1;
        return $res;
    }





    /**
     * 玉蒲团开奖结果
     * @param $res
     * @return array
     */
    public function  G32002($res)
    {

        $res['view_html'] = '';
        $betObj = $res['result'];
        $retObj =  $betObj->Result;
        for ($i=0;$i < 3 ;$i++ )
        {


            $col1 ='<tr>';
            $col1.='<td colspan="3">';
            $col1.='<div style="text-align: center; margin: 0 auto">';
            for ($n=0;$n < 3 ;$n++ )
            {
                $col1.= '<span><img src="../../gameImg/labaji/32002/ico_slots_ol9_'.$retObj[$i][$n].'.png" style="width: 60px;height: 60px;"></span>';
            }
            $col1.='</div>';
            $col1.='</td></tr>';
            $res['view_html'].=$col1;
        }

        $res['view_html'].=$this->getLineScoreList($betObj,9);
        return $res;
    }


    /**
     * 翻牌机开奖结果
     * @param $res
     * @return array
     */
    public function  getFpjDetail($res)
    {
        //{"ResultTemp":null,"InitBoard":[28,15,8,6,10,22,44,2,48,37,31,36,27,6,17,43,2,22,17,9,37,48,10,28,3,4,23,20,14,18],"ReplaceBoard":[0,37,50,22,16],"Reward":20000,"RewardTimes":2,"Bet":10000}
        $betObj = $res['result'];
        $res['view_html'] = '';
        $col1 ='<tr>';

        if($betObj->InitBoard!=null)
        {
            $col1.= '<td>开奖牌</td>';
            for ($n=0;$n < count($betObj->ReplaceBoard) ;$n++ )
            {
                $col1.= '<td><img src="../../gameImg/poker/POKER'.$betObj->ReplaceBoard[$n].'.png" style="width: 60px;height: 60px;"></td>';
            }
            $col1 .='<tr>';
            $col1.= '<td>萤分</td>';
            $col1.= '<td>'.($betObj->Reward/1000).'</td>';
            $col1.='</tr>';
        }
        else{
            $col1 .='<tr>';
            $col1.= '<td>翻倍萤分</td>';
            $col1.= '<td>'.(($betObj->RewardTimes)>1?$betObj->RewardTimes/1000:0).'</td>';
            $col1.='</tr>';
        }
        $col1.='</tr>';

        $res['view_html'].=$col1;
        return $res;
    }



    // hook model_show_bairen_result_end.php
    /**
     * 获取中奖线
     * @param $res
     */
    private  function  getLineScoreList($betObj,$lineCount=9)
    {

        $res['view_html']='';
        $countNumber = count($betObj->LineNumber);
        $col1 ='<tr style="text-align: center;background-color: #2b2b2b;color: White">';
        $col1.= '<td>线号</td>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>萤分</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;

        for($i = 0; $i <  $lineCount ;$i++)
        {
            $money = 0 ;
            for($n= 0 ; $n < $countNumber;$n++)
            {
                if($betObj->LineNumber[$n] == ($i+1) )
                {
                    $money = $betObj->LineNumberMoney[$n]/1000 ;
                }
            }

            $col1 ='<tr>';
            $col1.= '<td>'.($i+1).'号线</td>';
            $col1.= '<td>'.($betObj->Bet/$lineCount/1000).'</td>';
            $col1.= '<td>'.$money.'</td>';
            $col1.='</tr>';
            $res['view_html'].= $col1;
        }

        $col1 ='<tr style="color: red">';
        $col1.= '<td>总合计</td>';
        $col1.= '<td>'.($betObj->Bet/1000).'</td>';
        $col1.= '<td>'.($betObj->WinReward/1000).'</td>';
        $col1.='</tr>';
        $res['view_html'].= $col1;
        return  $res['view_html'];

    }
    /**
     * 获取水浒财神特殊中奖线
     * @param $res
     */
    private  function  getSpecalLineScoreList($betObj,$lineCount=9)
    {

        $res['view_html']='';
        $countNumber = count($betObj->LineNumber);
        $col1 ='<tr style="text-align: center;background-color: #2b2b2b;color: White">';
        $col1.= '<td>线号</td>';
        $col1.= '<td>压住</td>';
        $col1.= '<td>萤分</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        $lineMoneySumArr = [];
        for ($i =0 ;$i <$countNumber;$i++)
        {
            $lineMoneySumArr[$betObj->LineNumber[$i].'_'] += $betObj->LineNumberMoney[$i]/1000;
            if($betObj->LineNumber[$i] > 10)
            {
                $lineMoneySumArr[($betObj->LineNumber[$i]-10).'_'] += $betObj->LineNumberMoney[$i]/1000;
                $lineMoneySumArr[$betObj->LineNumber[$i].'_'] = 0;
            }
        }

        for($i = 0; $i <  $lineCount ;$i++)
        {
            $money = 0 ;
            if(isset( $lineMoneySumArr[($i+1).'_']))
            {
                $money = $lineMoneySumArr[($i+1).'_']  ;
            }
            $col1 ='<tr>';
            $col1.= '<td>'.($i+1).'号线</td>';
            $col1.= '<td>'.($betObj->Bet/$lineCount/1000).'</td>';
            $col1.= '<td>'.$money.'</td>';
            $col1.='</tr>';
            $res['view_html'].= $col1;
        }

        $col1 ='<tr style="color: red">';
        $col1.= '<td>总合计</td>';
        $col1.= '<td>'.($betObj->Bet/1000).'</td>';
        $col1.= '<td>'.($betObj->WinReward/1000).'</td>';
        $col1.='</tr>';
        $res['view_html'].= $col1;
        return  $res['view_html'];

    }



    /**
     * 德州牛仔
     * @param $res
     * @return mixed
     */
    public function G90001($res)
    {

        //{"ResultTemp":null,"CowboyPoker":[4,40],"BullfightingPoker":[8,36],"MiddlePoker":[26,41,45,49,51],"ResultArea":[1,10],"BetArea":[0,6000,0,7000,0,1000,7000,0,0,0,0,0,0],"Account":12000}

        $betObj = $res['result'];
        $res['view_html'] = '';
        $col1='<tr> 
           <td >信息</td>
            <td >和</td>
            <td >牛仔</td>
             <td >牛牛</td> 
             <td >同花</td> 
             <td >连牌</td>
             <td >对子</td>
             <td >同花连牌</td>
             <td >对A</td>
             <td >高排，一对</td>
             <td >连对</td>
             <td >三顺同</td>
             <td >葫芦</td> 
             <td >金同黄</td>
             </tr>';
        $col1 .='<tr>';
        $col1.='<tr style="color:green">';
        $col1.='<td>压住</td>';
        $col1.='<td>'.($betObj->BetArea[0]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[1]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[2]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[3]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[4]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[5]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[6]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[7]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[8]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[9]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[10]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[11]/1000).' </td>';
        $col1.='<td>'.($betObj->BetArea[12]/1000).' </td>';
        $col1 .='</tr>';


        $col1 .='<tr>';
        $col1.='<tr style="color:red">';
        $col1.='<td>萤分</td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(0 ,$betObj->ResultArea,$betObj->BetArea,20)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(1 ,$betObj->ResultArea,$betObj->BetArea,2)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(2 ,$betObj->ResultArea,$betObj->BetArea,1.9)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(3 ,$betObj->ResultArea,$betObj->BetArea,2.3)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(4 ,$betObj->ResultArea,$betObj->BetArea,3.3)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(5 ,$betObj->ResultArea,$betObj->BetArea,8.5)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(6 ,$betObj->ResultArea,$betObj->BetArea,12.5)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(7 ,$betObj->ResultArea,$betObj->BetArea,105)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(8 ,$betObj->ResultArea,$betObj->BetArea,2.2)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(9 ,$betObj->ResultArea,$betObj->BetArea,3.1)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(10 ,$betObj->ResultArea,$betObj->BetArea,4.8)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(11 ,$betObj->ResultArea,$betObj->BetArea,22)).' </td>';
        $col1.='<td>'.($this->getDZNZWinByIndex(12 ,$betObj->ResultArea,$betObj->BetArea,256)).' </td>';

        $col1 .='</tr>';
        $col1 .='<tr>';
        $col1.= '<td>牛仔牌</td>';
        $count =  count($betObj->CowboyPoker);
        for ($n=0;$n < 2 ;$n++ )
        {
            $col1.= '<td><img src="../../gameImg/poker/POKER'.$betObj->CowboyPoker[$n].'.png" style="width: 60px;height: 60px;"></td>';
        }
        $col1.='</tr>';

        $col1 .='<tr>';
        $col1.= '<td>中奖牌</td>';
        $count =  count($betObj->MiddlePoker);
        for ($n=0;$n < 5 ;$n++ )
        {
            $col1.= '<td><img src="../../gameImg/poker/POKER'.$betObj->MiddlePoker[$n].'.png" style="width: 60px;height: 60px;"></td>';
        }
        $col1.='</tr>';


        $col1 .='<tr>';
        $col1.= '<td>牛牛牌</td>';
        $count =  count($betObj->BullfightingPoker);
        for ($n=0;$n < 2 ;$n++ )
        {
            $col1.= '<td><img src="../../gameImg/poker/POKER'.$betObj->BullfightingPoker[$n].'.png" style="width: 60px;height: 60px;"></td>';
        }
        $col1.='</tr>';

        $col1.='<tr style="color:red">';
        $col1.= '<td>总萤分</td>';
        $col1.= '<td>'.($betObj->Account/1000).'</td>';
        $col1.='</tr>';
        $res['view_html'].=$col1;
        return $res;
    }


}


?>