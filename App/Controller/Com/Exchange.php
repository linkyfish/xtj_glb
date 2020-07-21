<?php

namespace Com;

use Ctrl\GameController;

Class Exchange extends GameController
{

	public function Index()
	{
		$agent = $this->default_user['id'];
		return $this->View(get_defined_vars());
	}

	public function Index_add()
	{
		$this->needadmin();
		$ymd = str_replace('-', '', $this->request->param('ymd'));
		empty($ymd) AND $this->response('0001');
		$agent_id = $this->request->param('agent_id');
		$value = $this->request->param('value', '');
		$user = $this->User->read_by_id($agent_id);
		$this->hspower($user);

		$sett = $this->UserSettlement->read(['Ymd' => $ymd, 'is_delete' => 0]);
		if ($sett['Ymd']) {
			$this->response('0001', [], '已经确认不可修改');
		}

		if ($user['RoleID'] == 9) {
			$stat = $this->UserStat->read(['Ymd' => $ymd, 'AgentID' => $user['id']]);
			if (empty($stat['Ymd'])) {
				$parent = $this->User->read_by_id($user['parent_id']);
				$glh = $this->User->read_by_id($parent['parent_id']);
				$row = [
					'Ymd' => $ymd,
					'AgentID' => $user['id'],
					'ParentID' => $user['parent_id'],
					'Add' => $value * $this->bl,
					'Rate' => $glh['share_rate'],
					'GLH' => $glh['id'],
					'CreateAT' => time(),
				];
				$this->UserStat->insert($row);
			} else {
				$this->UserStat->update(['Ymd' => $ymd, 'AgentID' => $user['id']], ['Add' => $value * $this->bl]);
			}

		} else if ($user['RoleID'] == 11) {
			$stat = $this->UserSettlement->read(['Ymd' => $ymd, 'AgentID' => $user['id'], 'is_delete' => 1]);
			if (empty($stat['Ymd'])) {
				$inset = ['Ymd' => $ymd, 'Rate' => $user['share_rate'], 'is_delete' => 1, 'UsrID' => $user['game_user_id'], 'Num' => $value * $this->bl, 'Add' => $value * $this->bl, 'AgentID' => $user['id'], 'Type' => $value > -1 ? 1 : -1];
				$inset['CreateAT'] = time();
				$this->UserSettlement->insert($inset);
			} else {
				$this->UserSettlement->update(['Ymd' => $ymd, 'AgentID' => $user['id'], 'is_delete' => 1], ['Add' => $value * $this->bl]);
			}
		}

		$this->response('0000');

	}

	public function Index_log()
	{
		$pageIndex = $this->request->param('page', 1);
		$pageSize = $this->request->param('limit', 20);
		$data = $this->UserSettlement->select(['AgentID' => $this->default_user['id']], ['Ymd' => -1], '*', 1, 30);
		foreach ($data as &$v) {

			$v['SumNum'] = calculate($v['SumNum']);
			$v['PoNum'] = calculate($v['PoNum']);
			$v['SubNum'] = calculate($v['SubNum']);
			$v['Num'] = calculate($v['Num']);
			$v['Sub'] = calculate($v['Sub']);
			$v['RealNum'] = calculate($v['RealNum']);
			$v['Add'] = calculate($v['Add']);

		}

		$this->response('0000', ['data' => $data]);
	}


	public function Index_need_confirm()
	{
		$type = $this->request->param('Status', 1);
		$ymd = $this->request->param('ymd', 0);
		$pageIndex = $this->request->param('page', 1);
		$pageSize = $this->request->param('limit', 20);
		$cond = ['Status' => $type];
		!empty($ymd) && $cond = ['PayAT' => ['>=' => strtotime($ymd . '000001'), '<=' => strtotime($ymd . '235959')]];
		$data = $this->UserSettlement->GetList($cond, ['Ymd' => -1], $pageIndex, $pageSize);
		$data['results'] = arrlist_multisort($data['results'], 'SubAT', false);
		foreach ($data['results'] as &$v) {
			$v['PayAT'] = $v['PayAT'] ? date('Y-m-d', $v['PayAT']) : '';
			$v['SumNum'] = calculate($v['SumNum']);
			$v['PoNum'] = calculate($v['PoNum']);
			$v['SubNum'] = calculate($v['SubNum']);
			$v['Num'] = calculate($v['Num']);
			$v['Sub'] = calculate($v['Sub']);
			$v['RealNum'] = calculate($v['RealNum']);
			$v['Add'] = calculate($v['Add']);
			$v['UserName'] = $this->agent_id_name[$v['AgentID']];
		}
		$this->response('0000', $data);
	}


	public function Index_stat()
	{
		$ymd = $this->request->param('ymd');
		$k = 'stat_lock_' . date('Ymd', strtotime($ymd . ' 00:00:00'));
		$lock = $this->UserStat->CacheGet($k);
		if (!$lock) {
			$this->PostTask('settlement', ['start' => $ymd . ' 00:00:00', 'end' => $ymd . ' 23:59:59']);
			//$this->UserStat->CacheSelect($k, $ymd);
			$this->response('0000');
		} else {
			$this->response('0001', [], '任务正在执行');
		}
	}

	/**
	 * 确认结算数据表
	 * Index_confirm
	 *
	 * @auth  true
	 * @login true
	 * @menu  false
	 * @throws \Exception
	 */
	public function Index_confirm()
	{
		$this->needadmin();
		$ymd = str_replace('-', '', $this->request->param('ymd'));
		empty($ymd) AND $this->response('0001');

		$k = 'stat_lock_' . $ymd;
		$lock = $this->UserStat->CacheGet($k);
		if ($lock) {
			$this->response('0001', [], '结算数据正在生成');
		}

		$k = 'Settconfirm' . $ymd;
		$var = $this->UserSettlement->CacheGet($k);
		if ($var) {
			$this->response('0001', [], '已确认');
		}
		$this->UserSettlement->CacheSet($k,$ymd);
		$list = $this->UserSettlement->select(['Ymd' => $ymd, 'is_delete' => 1]);
		count($list)==0 && $this->response('0001', [], '已确认');
		foreach ($list as $v) {
			if ($v['Num'] < 1) {
				$this->UserSettlement->update(['Ymd' => $ymd, 'AgentID' => $v['AgentID'], 'is_delete' => 1], ['is_delete' => 0, 'Status' => 2, 'Type' => -1, 'SubAT' => time()]);
				$this->User->update(['id' => $v['AgentID']], ['balance' => $v['Num']]);
			} else {
				$this->User->update(['id' => $v['AgentID']], ['balance' => 0]);
				$this->UserSettlement->update(['Ymd' => $ymd, 'AgentID' => $v['AgentID'], 'is_delete' => 1], ['is_delete' => 0]);
			}

			//$this->UserSettlement->sum('Num',['AgentID'=>$v['AgentID'],'is_delete'=>0]);
			//$this->User->update(['id' => $v['AgentID']], ['balance' => $v['Num']]);
		}


		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '审核结算');
		$this->UserSettlement->CacheDel($k);
		$this->response('0000');
	}


	/**
	 * 结算日期
	 * Index_date
	 *
	 * @auth  true
	 * @login true
	 * @menu  false
	 * @throws \Exception
	 */
	public function Index_date()
	{
		$this->needadmin();
		$start_ym = $this->request->param('Ym', date('Ym'));
		if (empty($start_ym)) {
			$start_ym = date('Ym');
		}

		$start = $this->MoneyTrans->find_one([], ['id' => 1], 'times');
		$_start_ym = date('Ymd', strtotime($start['times']));

		$ymd = $this->UserStat->select([], [], 'Ymd', 0, 0, '', 'Group By Ymd');
		$ymd = arrlist_values($ymd, 'Ymd');
		$time = strtotime($start_ym . '01000000');
		$_time = min(time(), strtotime(date('Ymt235959', $time)));
		$list = $this->UserSettlement->select(['CreateAT' => ['>=' => $time - 864000]]);
		$count = [];
		$agent_son = arrlist_values($this->agent_son[$this->agent_first['id']], 'id');
		$list=arrlist_multisort($list,'Ymd',true);
		$ymd_array=[];
		foreach ($list as $v) {
			$ymd_array[$v['Ymd']]= $v['Ymd'];
			if ($v['Num'] > 0) {
				$count[$v['Ymd']]['count'] = bcadd($count[$v['Ymd']]['count'], calculate($v['Num']), 2);
			} else {
				$count[$v['Ymd']]['sub'] = bcadd($count[$v['Ymd']]['sub'], calculate($v['Num']), 2);
			}
			if (in_array($v['AgentID'], $agent_son)) {
				$count[$v['Ymd']]['SumNum'] = bcadd($count[$v['Ymd']]['SumNum'], calculate($v['SumNum']), 2);
			}
		}

		$list = $this->UserSettlement->select(['PayAT' => ['>=' => $time, '<=' => $_time]]);
		foreach ($list as $v) {
			$_pymd = date('Ymd', $v['PayAT']);
			empty($count[$_pymd]['scount']) && $count[$_pymd]['scount']=0;
			$count[$_pymd]['scount'] = bcadd($count[$_pymd]['scount'], calculate($v['Num']), 2);
		}

		$data = [];
		$now = date('Ymd');
		$i=0;
		$y=0;
		foreach ($ymd_array as $k => $v){
			$row=$count[$v];
			$row['ok']=1;
			$row['ymd_fmt'] = date('Y-m-d',strtotime($v.'000000'));
			$row['ymd'] = date('Y-m-d',strtotime($v.'000000'));
			empty($row['scount']) && $row['scount']="0.00";
			empty($row['SumNum']) && $row['SumNum']="0.00";
			empty($row['sub']) && $row['sub']="0.00";
			//in_array($v, (array)$ymd) && $row['ok'] = 1;
			($v >= $now || $v <= $_start_ym) && $row['ok'] = 2;
			$row['subnew'] = min(0, bcadd($row['sub'], -$count[$y]['sub'], 2));
			if($i && $v>=date('Ym01', $time) && $v<=date('Ymt', $time)){
				if($i!=$row['ymd_fmt']){
					$min = intval(str_replace('-','',$i));
					$max = intval(str_replace('-','',$row['ymd_fmt']));
					$row['scount']="0.00";
					$row['sub']="0.00";
					foreach ($count as $ym => $d){
						$_ym = intval(str_replace('-','',$ym));
						//$row['scount'].=$min.' '.$max.' '.$_ym.' - ';
						if($_ym>=$min && $_ym<=$max){
							//$row['scount'].=$min.' '.$max.' '.$_ym;
							$row['scount']=bcadd($row['scount'],$d['scount'],2);
							$row['sub']=bcadd($row['sub'],$d['sub'],2);
						}
					}
				}
				$row['subnew'] = min(0, bcadd($row['sub'], -$count[$y]['sub'], 2));
				$row['ymd_fmt']=$i.' ~ '.$row['ymd_fmt'];
				$data[]=$row;
			}
			$i = date('Y-m-d',strtotime($v.'000000')+86400);
			$y=$k;
		}
		(date('Ymd')<=date('Ymt', $time) && date('Ymd',strtotime('-2 day'))>date('Ymd',strtotime($i.' 00:00:00'))) && $data[]=['ymd'=>date('Y-m-d',strtotime('-1 day')),'ymd_fmt'=>$i.' ~ '.date('Y-m-d',strtotime('-1 day')),'ok'=>in_array(date('Ymd',strtotime('-1 day')),$ymd)];
		$data = arrlist_multisort($data, 'ymd', false);
		$this->response('0000', ['data' => $data]);
	}


	/**
	 * Index_data
	 *
	 * @auth  true
	 * @login true
	 * @menu  false
	 * @throws \Exception
	 */
	public function Index_data()
	{
		$this->needadmin();
		$ymd = $this->request->param('ymd');
		empty($ymd) AND $this->response('0001');
		$_ymd = str_replace('-', '', $ymd);

		$kv = $this->Kv->read('agent_'.str_replace('-','',$ymd));
		$kv = arrlist_change_key($kv,'id');
		$cond = ['RoleID' => [2, 9, 10, 11]];
		$list = $this->User->select($cond, [], 'id,parent_id,RoleID,share_rate,game_user_id,balance,username');
		foreach ($list as &$agent_v){
			$agent_v['parent_id']=$kv[$agent_v['id']]['parent_id'];
		}
		$list = arrlist_multisort($list,'parent_id',true);
		$agent_id_value = arrlist_change_key($list, 'id');
		$agent_son = [];
		foreach ($list as $v) {
			!isset($agent_son[$v['id']]) && $agent_son[$v['id']] = [];
			!isset($agent_son[$v['parent_id']]) && $agent_son[$v['parent_id']] = [];
			$agent_son[$v['parent_id']][] = ['id' => $v['id'], 'R' => $v['RoleID']];
		}

		$stat = arrlist_change_key($this->UserStat->select(['Ymd' => $_ymd]), 'AgentID');
		$lock_sess = $this->UserSettlement->read(['Ymd' => $_ymd, 'is_delete' => 0]);

		if (empty($lock_sess['Ymd'])) {
			$stat_gl = arrlist_change_key($this->UserSettlement->select(['Ymd' => $_ymd, 'is_delete' => 1]), 'AgentID');
		} else {
			$stat_gl = arrlist_change_key($this->UserSettlement->select(['Ymd' => $_ymd, 'is_delete' => 0]), 'AgentID');
		}
		//var_export($stat_gl);
		$data = [];
		foreach ($list as $v) {

			$k = $v['id'];
			$parent_id = $agent_id_value[$k]['parent_id'];//主号
			$zhid = $agent_id_value[$parent_id]['parent_id'];//管理号
			$glh = $agent_id_value[$zhid];//管理号

			$row = [];
			if ($v['RoleID'] == 11 || $v['RoleID'] == 2) {
				$row['Rate'] = $v['share_rate'];
			} else if ($v['RoleID'] == 10) {
				$row['Rate'] = $agent_id_value[$parent_id]['share_rate'];
			} else if ($v['RoleID'] == 9) {
				$row['Rate'] = $glh['share_rate'];
			}
			isset($stat[$v['id']]) && $row['Rate'] =$stat[$v['id']]['Rate'];
			isset($stat_gl[$v['id']]) && $row['Rate'] =$stat_gl[$v['id']]['Rate'];

			if (isset($stat[$v['id']])) {
				//$row['Rate'] = $stat[$v['id']]['Rate'];
				$row['Add'] = $stat[$v['id']]['Add'];
				$row['Sdd'] = $stat[$v['id']]['Add'];
				$row['Z'] = bcadd($stat[$v['id']]['Game_Win'], $stat[$v['id']]['Poker_Pump']);
				$row['Z'] = bcsub($row['Z'], $stat[$v['id']]['Reg_Ad']);
				$row['Z'] = bcsub($row['Z'], $stat[$v['id']]['Active_Ad']);//总数据
				$row['ZC'] = bcmul($row['Z'], $row['Rate'] * 0.01);//占成数据
				$row['SG'] = bcmul($row['Add'], $row['Rate'] * 0.01);//占成数据
				$row['G'] = bcsub($row['ZC'], $stat[$v['id']]['Apply_Ad']);//管理数据
				$row['Apply_Ad'] = $stat[$v['id']]['Apply_Ad'];//管理数据

				$this->add_sum($v['id'], $parent_id, $row, $agent_id_value, $data);
			} else {
				$row['Add'] = 0;
				$row['Sdd'] = 0;
				$row['Z'] = 0;
				$row['ZC'] = 0;
				$row['G'] = 0;
			}

			$data[$v['id']]['game_user_id'] = $v['game_user_id'];
			$data[$v['id']]['id'] = $v['id'];
			$data[$v['id']]['Add'] = $row['Add'];
			$data[$v['id']]['Sdd'] = $row['Sdd'];
			$data[$v['id']]['G'] = $row['G'];
			$data[$v['id']]['balance'] = $v['balance'];
			$data[$v['id']]['parent_id'] = $v['parent_id'];
			$data[$v['id']]['RoleID'] = $v['RoleID'];
			$data[$v['id']]['Rate'] = $row['Rate'];
			$data[$v['id']]['username'] = $v['username'];
			if ($agent_id_value[$v['parent_id']]['RoleID'] == 2) {
				$data[$v['parent_id']]['lock'] = 1;
				$data[$v['id']]['lock'] = 1;
			} else {
				$data[$v['id']]['lock'] = 0;
			}
		}

		$_data = [];
		$islock = $lock_sess['Ymd'] ? 1 : 0;
		$rate = 1.03;
		$count_count = 0;
		$Sub = 0;

		foreach ($data as $v) {
			$scount = 0;
			if ($v['RoleID'] == 9 || $v['RoleID'] == 10) {
				$v['count'] = $v['ZC'];//实际数据=占成-分成(0)
			} else if ($v['RoleID'] == 11) {

				$v['ZC'] = bcmul($v['Z'], $v['Rate'] * 0.01);
				$v['SZC'] = bcmul($v['Add'], $v['Rate'] * 0.01);
				$v['G'] = 0;
				$v['SG'] = 0;
				$v['Sub'] = 0;
				$v['Add'] = 0;
				$v['Apply_Ad']=0;
				foreach ($agent_son[$v['id']] as $son) {
					if ($son['R'] != 10) {
						//$v['G'] = bcadd($v['G'], bcsub(bcmul($data[$son['id']]['Z'], $data[$son['id']]['Rate'] * 0.01), $stat[$v['id']]['Active_Ad']));
						$v['G'] = bcadd($v['G'], bcmul($data[$son['id']]['Z'], $data[$son['id']]['Rate'] * 0.01));
						$v['SG'] = bcadd($v['SG'], bcmul($data[$son['id']]['Add'], $data[$son['id']]['Rate'] * 0.01));
					}else{
						foreach ($agent_son[$son['id']] as $s){
							$v['Apply_Ad']=bcadd($v['Apply_Ad'],$stat[$s['id']]['Apply_Ad']);
						}
					}
				}

				$v['count'] =bcsub( bcsub($v['ZC'], $v['G']),$v['Apply_Ad']);//实际数据=占成-分成
				$v['Add'] = bcsub($v['SZC'], $v['SG']);//实际数据=占成-分成

				if ($stat_gl[$v['id']]) {
					$v['Add'] = bcadd($v['Add'], $stat_gl[$v['id']]['Add']);
					$v['Sdd'] = $stat_gl[$v['id']]['Add'];
					$v['Sub'] = $stat_gl[$v['id']]['Sub'];
				}

				$sub = bcadd($v['count'], $v['Sub']);
				if ($sub < 0) {
					$v['scount'] = bcadd(bcadd($v['count'], $v['Add']), $v['Sub']);
				} else {
					$v['scount'] = bcadd(bcmul(bcadd($v['count'], $v['Sub']), $rate), $v['Add']);
				}

			} else {
				continue;
			}

			if ($v['RoleID'] == 11 && empty($lock_sess['Ymd'])) {
				if ($v['balance'] < 0) {
					$v['Sub'] = $v['balance'] < 0 ? $v['balance'] : 0;
					$sub = bcadd($v['count'], $v['Sub']);
					if ($sub < 0) {
						$v['scount'] = bcadd(bcadd($v['count'], $v['Add']), $v['Sub']);
					} else {
						//$v['scount'] = bcadd(bcadd(bcmul($v['count'], $rate), $v['Add']), $v['Sub']);
						$v['scount'] = bcadd(bcmul(bcadd($v['count'], $v['Sub']), $rate), $v['Add']);
						$v['scount'] = round(calculate($v['scount'])) * $this->bl;
					}
				} else {
					$v['scount'] = round(calculate($v['scount'])) * $this->bl;
				}

				$sett = $this->UserSettlement->read(['Ymd' => $_ymd, 'AgentID' => $v['id'], 'is_delete' => 1]);
				$inset = ['SumNum' => $v['Z'], 'Sub' => $v['Sub'], 'PoNum' => $v['ZC'], 'SubNum' => $v['G'], 'Num' => $v['scount'], 'RealNum' => $v['count'], 'Rate' => $v['Rate'], 'UsrID' => $v['game_user_id'], 'Type' => $v['scount'] >= 0 ? 1 : -1];
				if (empty($sett['Ymd'])) {
					$inset['CreateAT'] = time();
					$inset['Ymd'] = $_ymd;
					$inset['AgentID'] = $v['id'];
					$inset['is_delete'] = 1;
					$this->UserSettlement->insert($inset);
				} else if (!empty($sett['is_delete'])) {
					$this->UserSettlement->update(['Ymd' => $_ymd, 'AgentID' => $v['id'], 'is_delete' => 1], $inset);
				}
			} else {

				$sub = bcadd($v['count'], $v['Sub']);
				if ($sub < 0) {
					//$v['scount']=bcadd(bcadd($v['count'],$v['Add']),$v['Sub']);
				} else {
					// $v['scount']=bcadd(bcadd(bcmul($v['count'],$rate),$v['Add']),$v['Sub']);
					$v['scount'] = round(calculate($v['scount'])) * $this->bl;
				}

				//$v['scount']= round(calculate($v['scount']))*$this->bl;
			}

			//$v['count']=bcmul($v['count'],$rate);


			$Sub = bcadd($Sub, $v['Sub']);
			$count_count = bcadd($count_count, $v['scount']);
			$v['scount'] = calculate($v['scount']);
			$v['count'] = calculate($v['count']);
			$v['Add'] = calculate($v['Add']);
			$v['Sdd'] = calculate($v['Sdd']);
			$v['Sub'] = calculate($v['Sub']);
			$v['Z'] = calculate($v['Z']);
			$v['ZC'] = calculate($v['ZC']);
			$v['G'] = calculate($v['G']);
			$v['open'] = $v['RoleID'] == 11 ? "true" : "false";
			$_data[] = $v;
		}
		$_data = arrlist_multisort($_data,'id',true);
		unset($data);
		$this->response('0000', ['data' => $_data, 'islock' => $islock, 'scount' => calculate($count_count), 'sub' => calculate($Sub)]);
	}

	public function add_sum($agentid, $parent_id, $row, $agent_id_value, &$data)
	{
		$data[$agentid]['Add'] += $row['Add'];
		$data[$agentid]['Z'] += $row['Z'];
		$data[$agentid]['ZC'] += $row['ZC'];
		$data[$agentid]['SG'] += $row['SG'];
		$data[$agentid]['G'] += $row['G'];
		$data[$agentid]['Apply_Ad'] += $row['Apply_Ad'];
		$zhid = $agent_id_value[$parent_id]['parent_id'];//主号
		if ($parent_id) {
			//$row['Add']<0 && $row['Add']=0;
			$this->add_sum($parent_id, $zhid, $row, $agent_id_value, $data);
		}
	}


}
