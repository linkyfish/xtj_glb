<?php

namespace Com;

use Ctrl\GameController;
use itbdw\Ip\IpLocation;

Class Stat extends GameController
{

    public function Index()
    {
    	$this->needadmin(1);

        return $this->View(get_defined_vars());
    }

    public function online(){
		$cond = ['ai'=>0];
		$cond['online'] = ['>' => 0];
		$list = $this->Usr->select($cond,[],'online,roomid,last_login_ip');
		$games = $this->Game->gamename;
		$array=[];
		$_array=[];
		foreach ($list as $k => $v) {
			empty($array[$v['roomid']]) && $array[$v['roomid']]=['Name'=>$games[$v['roomid']]['Name'],'Num'=>0];
			$array[$v['roomid']]['Num']+=1;
			$area = IpLocation::getLocation($v['last_login_ip']);
			empty($area['province']) && $area['province']=$area['country'];
			empty($_array[$area['province']]) && $_array[$area['province']]=['Name'=>$area['province'],'Num'=>0];
			$_array[$area['province']]['Num']+=1;
		}

		$data['online']=['games'=>arrlist_values($array,'Name'),'online'=>arrlist_values($array,'Num')];
		$data['area']=['city'=>arrlist_values($_array,'Name'),'online'=>arrlist_values($_array,'Num')];
		$this->response('0000', $data);
	}


    public function History(){
		$ymd = $this->request->param('ymd',date('Y-m-d',strtotime('-7 Day')));
		$ymd.=' 00:00:00';
		$ymd_arr = $this->MoneyTrans->getDatesBetweenTwoDays($ymd,date('Y-m-d 23:59:59',strtotime($ymd)+86400*7));
		$cond =['regtime'=>['>='=>$ymd,'<='=>date('Y-m-d 23:59:59',strtotime($ymd)+86400*7)],'ai'=>0];
		$data = $this->Usr->select($cond);
		$reg_arr=[];
		$ymd_arr=array_reverse($ymd_arr);
		foreach ($ymd_arr as $v){
			$reg_arr[$v]['ymd']=$v;
			$reg_arr[$v]['num']=0;
			$reg_arr[$v]['pay_money']=0;
			$reg_arr[$v]['pay_num']=0;

			$reg_arr[$v]['pay_on_1']=0;
			$reg_arr[$v]['pay_money_1']=0;
			$reg_arr[$v]['pay_num_1']=0;
			$reg_arr[$v]['pay_on_2']=0;

			$reg_arr[$v]['pay_money_2']=0;
			$reg_arr[$v]['pay_num_2']=0;

			$reg_arr[$v]['pay_on_3']=0;
			$reg_arr[$v]['pay_on_4']=0;
			$reg_arr[$v]['pay_on_5']=0;
			$reg_arr[$v]['pay_on_6']=0;
		}

		foreach ($data as $row){
			$row['reg']=explode(' ',$row['regtime'])[0];
			$reg_arr[$row['reg']]['usr'][]=$row['usrid'];
			$reg_arr[$row['reg']]['num']+=1;
		}

		foreach ($reg_arr as &$v){
				for($i=0;$i<7;$i++){
					$ymd = date('Ymd',strtotime($v['ymd'])+($i*86400));
					if($i<3){
						if($v['usr']){
							$sum = $this->UsrMoney->select(['ymd'=>$ymd,'usrid'=>$v['usr']],'','usrid,Sg_Ad,Sc_Ad');
							foreach ($sum as $_sum){
								$v['pay_money'.($i?'_'.$i:'')]+=($_sum['Sc_Ad']+$_sum['Sg_Ad']);
								($_sum['Sc_Ad']+$_sum['Sg_Ad'])>0 && $v['pay_num'.($i?'_'.$i:'')]+=1;
							}
							$v['pay_money'.($i?'_'.$i:'')]=calculateSub2($v['pay_money'.($i?'_'.$i:'')]);
						}
					}

					if($i>0) {
						if ($v['usr']) {
							$ol = $this->PlayerOline->select(['ymdhi' => ['>=' => $ymd . '0000', '<=' => $ymd . '2359'], 'uid' => $v['usr']]);
							$v['pay_on' . ($i ? '_' . $i : '')] = count(array_filter(array_unique(arrlist_values($ol, 'uid'))));
						}
					}
				}
		}
		$this->response('0000',['data'=>array_values($reg_arr)]);
	}
    public function Reg_history(){
		$ymd = $this->request->param('ymd');
		if(!empty($ymd)){
			list($start,$end)= explode(' - ',$ymd);
			$cond =['regtime'=>['>='=>date('Y-m-d 00:00:00',strtotime($start.' 00:00:00')),'<='=>date('Y-m-d 23:59:59',strtotime($end.' 23:59:59'))],'ai'=>0];
		}else{
			$cond =['ai'=>0];
		}

		$data = $this->Usr->select($cond,[],'regip,usrid');
		$array=[];
		$usrid = arrlist_values($data,'usrid');
		$sum = $this->UsrMoney->select(['usrid'=>$usrid],'','usrid,Sg_Ad,Sc_Ad,Sg_Sub,Cash_Ad,Cash_C_Ad');
		$sum = arrlist_group($sum,'usrid');

		foreach ($data as $row){
			$area = IpLocation::getLocation($row['regip']);
			empty($area['province']) && $area['province']=$area['country'];
			empty($array[$area['province']]) && $array[$area['province']]=['Name'=>$area['province'],'Num'=>0,'Add'=>0,'AddMoney'=>0,'Cash'=>0,'CashMoney'=>0];
			$array[$area['province']]['Num']+=1;
			if(!empty($sum[$row['usrid']])){
				$i=0;
				$ii=0;
				foreach ($sum[$row['usrid']] as $v){
					if(($v['Sg_Ad']+$v['Sc_Ad'])>0){
						$i=1;
						$array[$area['province']]['AddMoney']+=$v['Sg_Ad']+$v['Sc_Ad'];
					}
					if(($v['Sg_Sub']+$v['Cash_Ad']+$v['Cash_C_Ad'])<0){
						$ii=1;
						$array[$area['province']]['CashMoney']+=$v['Sg_Sub']+$v['Cash_Ad']+$v['Cash_C_Ad'];
					}

				}
				$array[$area['province']]['Add']+=$i;
				$array[$area['province']]['Cash']+=$ii;
			}
		}

		$this->response('0000',['data'=>array_values($array)]);
	}


}
