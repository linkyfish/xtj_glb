<?php
namespace Model;


// hook model_usr_use.php

class UsrModel extends \App\Model
{
    // hook model_usr_public_start.php
    public $table = 'usr';
    public $index = 'usrid';
    // hook model_usr_public_end.php

    // hook model_usr_start.php


    public function RegDate($sData,$eData,$cond=[]){
		$cond['ai']=0;
        $cond['regtime'] = ['>=' => $sData.' 00:00:00', '<=' => $eData.' 23:59:59'];
        $list = $this->select($cond,[],"usrid,openid,regtime");

        $data=[];
        foreach ($list as $v){
            list($regtime,) = explode(' ',$v['regtime']);
            $v['regdate']=$regtime;
            //$data['list'][]=$v;
            $data['TotalNum'][$regtime]+=1;
            $data['Total']+=1;
        }
        return $data;
    }

    // hook model_usr_end.php

}
?>