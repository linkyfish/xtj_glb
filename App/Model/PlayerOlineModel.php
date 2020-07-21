<?php
namespace Model;

class PlayerOlineModel extends \App\Model {
    //public $link='db1';
    public $table = 'playeroline';
    public $index ='';


    public function LoginDate($dates,$cond=[]){
        $data=[];
        foreach ($dates as $date){
            $d =  str_replace('-','',$date);
            $cond['ymdhi'] = ['>=' => $d.'0000', '<=' => $d.'5959'];
            $list = $this->select($cond,[],'max(gameid) gameid,uid',0,0,'','group by uid');
            $home=0;
            $game=0;
            foreach ($list as $v){
                $home+=1;
                $v['gameid']>1 && $game+=1;
            }
            $data[$date]['home']+=$home;
            $data[$date]['game']+=$game;
        }
        return $data;
    }
}