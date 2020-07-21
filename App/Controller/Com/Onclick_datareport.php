<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_datareport extends GameController {

    public function Index () {
        $sid = $this->request->param('sid',0);
        $sDate = $this->request->param('sDate', '');
        $eDate = $this->request->param('eDate', '');
		$this->needadmin();
		$tpl='Onclick_datareport_';
        switch ($sid){
            case 1:
				$tpl.='1.Index';
				$userlist = $this->Usr->select(['ai'=>0,'regtime'=>['>='=>$sDate.' 00:00:00','<='=>$eDate.' 23:59:59']],['usrid'=>-1]);
				$usrids = arrlist_values($userlist,'usrid');
				$song = $this->Gamebilog->select(['UsrID'=>$usrids,'Type'=>25],[],'UsrID,Money');
				$song = arrlist_key_values($song,'UsrID','Money');
				foreach ($userlist as &$v){
					$v['regsong']=$v['reg_give']==2?'忽略':($song[$v['usrid']]?calculate($song[$v['usrid']]):'未赠送');
				}
                break;
            case 2:
            case 3:
				$tpl.=$sid.'.Index';
				$_sDate =  str_replace('-','',$sDate);
				$_eDate =  str_replace('-','',$eDate);
				$cond['ymdhi'] = ['>=' => $_sDate.'0000', '<=' => $_eDate.'5959'];
				$list = $this->PlayerOline->select($cond,[],'max(gameid) gameid,uid',0,0,'','group by uid');
				$usrid=[];
				if($sid==2){
					foreach ($list as $v){
						$v['gameid']>1 && $usrid[]=$v['uid'];
					}
				}else{
					$usrid = arrlist_values($list,'uid');
				}
				$userlist = $this->Usr->select(['usrid'=>$usrid],['usrid'=>-1]);
				$start = strtotime($sDate . ' 00:00:00');
				$end = strtotime($eDate . ' 23:59:59');
				foreach ($userlist as &$_v) {
					$row = $this->stat_player_start_end($_v, $start, $end,$this->roleInfo);
					$_v['win']=$row['win'];
				}
                break;
			case 4:
				$tpl.=$sid.'.Index';
				!$this->roleInfo['czs'] && $this->response('0001',[],'权限不足');
				$cond = ['times'=>['>=' => $sDate.' 00:00:00', '<=' => $eDate.' 59:59:59'],'style'=>[51,61]];
				$list = $this->MoneyTrans->select($cond,[],'sum(chips) chips,usrid',0,0,'','group by usrid');
				$usrs = arrlist_key_values($list,'usrid','chips');
				$userlist = $this->Usr->select(['usrid'=>arrlist_values($list,'usrid')],['usrid'=>-1]);
				$start = strtotime($sDate . ' 00:00:00');
				$end = strtotime($eDate . ' 23:59:59');
				foreach ($userlist as &$_v) {
					$row = $this->stat_player_start_end($_v, $start, $end,$this->roleInfo);
					$_v['win']=$row['win'];
					$_v['chips']=calculate($usrs[$_v['usrid']]);
				}
        }

        return $this->View(get_defined_vars(),$tpl);
    }
}
