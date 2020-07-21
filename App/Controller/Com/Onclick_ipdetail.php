<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_ipdetail extends GameController {

    public function Index () {
        $this->needadmin();
        $acc= $this->request->param('acc','');
        $e = $this->request->param('e','');
        if(is_numeric($e)&&$e>11000000000){
            $user =  $this->Usr->read(['openid'=>$e],'regip,last_login_ip');
        }else{
            $user =  $this->Usr->read(['usrid'=>$e],'regip,last_login_ip');
        }

        switch ($acc){
            case 'RegisterIP':
                $acc='regip';
                !empty($user) AND  $e=$user['regip'];
                break;
//            case 'MacAddress':
//                $acc='mac';
//                $e=empty($user) ? base64_decode($e):$user['MacAddress'];
//                break;
            case 'LatestLoginIP':
                $acc='last_login_ip';
                !empty($user) AND  $e=$user['last_login_ip'];
                break;
        }

        $userlist = $this->Usr->select([$acc=>$e]);
        $time = time();

        foreach ($userlist as $k=> $v){
            $userlist[$k]=$this->Account->format($v,$time,$this->roleInfo,$this->agent_id_name);
        }

        return $this->View(get_defined_vars());
    }
//
//    public function getUserInfo($zd,$val)
//    {
//        $result = $this->gameapi->get_user_list(0,[$zd=>$val],1,50,0);
//        $ret=[];
//        if ($result['retcode'] == 0 && $result['data']) {
//            $uids= arrlist_values($result['data'],'uid');
//            $gamebilog =$this->Gamebilog->select(['UID'=>$uids,'Type'=>[30,50,37,38]],[],'sum(Number) as Number,UID',0,0,'','group by UID');
//            $time = time();
//            $userinfo = $this->UserAccount->select(['ID'=>$uids],[],'ID,LoginName');
//            $userinfo = arrlist_key_values($userinfo,'ID','LoginName');
//            $gamebilog = arrlist_key_values($gamebilog,'UID','Number');
//            //var_dump($uids,$userinfo);
//            //var_dump($gamebilog);
//            foreach ($result['data'] as $k => $v) {
//                $ol = strtotime($v['lastlogintime']);
//                $vv = [];
//                $vv['idx'] = $v['uid'];
//                $vv['Account'] = $this->ycphone($userinfo[$v['uid']]);
//                $vv['DisOnlineDay'] = min(99999, intval(($time - $ol) / 3600));
//                $vv['Online'] = $time - $ol > 60 ? 99999 : 1;
//                $vv['Status'] = $v['status'];
//                $vv['Balance'] = $this->calculate($v['gamebi'] + $v['bankgamebi']);
//                $vv['Win'] = $this->calculate($v['HistoryWin'] );
//                $vv['ScoreNum'] =$this->calculate((isset($gamebilog[$v['uid']]) ? $gamebilog[$v['uid']]:0) ) ;
//                $ret[]=$vv;
//            }
//        }
//        return $ret;
//    }
}
