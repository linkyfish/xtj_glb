<?php

namespace Com;

use Ctrl\GameController;

Class Com_ip extends GameController {

    public function Index () {


        //$MacAddress = $this->UserAccount->select([],[],'MacAddress,count(1) as counts',0,0,'','group by MacAddress having counts>1');
        $RegisterIP = $this->Usr->select(['ai'=>0],[],'regip,count(1) as counts',0,0,'','group by regip having counts>1');
        $LatestLoginIP = $this->Usr->select(['ai'=>0],[],'last_login_ip,count(1) as counts',0,0,'','group by last_login_ip having counts>1');

//        foreach ($MacAddress as $k=> $v){
//            if(empty($v['MacAddress'])){
//                unset($MacAddress[$k]);
//            } else{
//                if(strpos($v["MacAddress"],"nonexistent-key")!==FALSE){
//                    unset($MacAddress[$k]);
//                }
//
//            }
//
//        }
//        $MacAddress=array_values($MacAddress);

        foreach ($RegisterIP as $k=> $v){
            if(empty($v['regip']) || $v['regip']=='127.0.0.1') unset($RegisterIP[$k]);
        }
        $RegisterIP=array_values($RegisterIP);

        foreach ($LatestLoginIP as $k=> $v){
            if(empty($v['last_login_ip'])|| $v['regip']=='127.0.0.1') unset($LatestLoginIP[$k]);
        }
        $LatestLoginIP=array_values($LatestLoginIP);

        return $this->View(get_defined_vars());
    }
}
