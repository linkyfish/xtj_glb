<?php

namespace Com;

use Ctrl\GameController;

Class Admin_stat extends GameController
{

	public function Index()
	{
		$this->needadmin();


		return $this->View(get_defined_vars());
	}

	public function Index_Data()
	{
		$this->needadmin();
//		$user = $total_win = $this->UsrMoney->select([],[],'sum(Game_Win) as Game_Win,usrid',0,0,'','Group by usrid');
//		foreach ($user as $v){
//			$this->Usr->update(['usrid'=>$v['usrid']],['total_win'=>-$v['Game_Win']]);
//		}

		$first = $this->MoneyTrans->find_one([], ['id' => 1]);
		$start = $first['times'];
		empty($start) && $start=date('Y-m-d H:i:s');
		$date = $this->MoneyTrans->getDatesBetweenTwoDays($start, date('Y-m-d H:i:s', time() + 86400));
		$data = $this->UsrMoney->select([], [], '*', 0, 0, 'ymd', 'Group By ymd');
		foreach ($date as &$row) {
			$ymd = $row;
			$row = $data[str_replace('-', '', $ymd)];
			$row['update_at'] && $row['update_at'] = date('Y-m-d H:i:s', $row['update_at']);
			$row['ymd'] = $ymd;
		}
		$date = array_reverse($date);
		$this->response('0000', ['results' => $date]);
	}

	public function Index_send()
	{
		$this->needadmin();
		$r = $this->Usr->CacheGet('stat_do_lock');
		if ($r) {
			$this->response('0001', [], '操作频繁，间隔>=10秒');
		}
		$this->Usr->CacheSet('stat_do_lock', time(), 10);
		$ymd = $this->request->param('ymd', date('Y-m-d'));
		$this->PostTask('moneytrans_minute', ['start' => strtotime($ymd . ' 10:10:10')]);
		$this->response('0000');
	}

	public function Index_reload()
	{
		$this->needadmin();
		$r = $this->Usr->CacheGet('stat_do_reload');
		if ($r) {
			$this->response('0001', [], '操作频繁，间隔>=10秒');
		}
		$this->Usr->CacheSet('stat_do_reload', time(), 10);
		$first = $this->MoneyTrans->find_one([], ['id' => 1]);
		$date = $this->MoneyTrans->getDatesBetweenTwoDays($first['times'], date('Y-m-d H:i:s', time()+83400));
		$date = array_reverse($date);
		$player_move = $this->UserMove->select(['Type' => 10], ['C_DateTime' => 1]);
		$player_move_uid =arrlist_values($player_move,'UID');
		$u = $this->Usr->select(['usrid'=>$player_move_uid],[],'usrid,agent_id');
		$u = arrlist_key_values($u,'usrid','agent_id');
		foreach ($u as $_k=> $_r){
			$this->UsrMoney->update(['usrid'=>$_k],['agent_id'=>$_r]);
		}

		foreach ($player_move as $v) {
			$last = str_replace('-', '', explode(' ', $v['C_DateTime'])[0]);
			$this->UsrMoney->update(['usrid' => $v['UID'], 'ymd' => ['>=' => $last]], ['agent_id' => $v['AfterAgentID']]);
			xn_log($v['UID'].' '.$last.' '.$v['AfterAgentID'],'move');
		}

		$agent_move = $this->UserMove->select(['Type' => 20], ['C_DateTime' => 1]);
		$agent_move = arrlist_group($agent_move, 'UID');
		$list = $this->User->select([], [], 'id,parent_id,RoleID');
		foreach ($date as $v) {
				$k = str_replace('-', '', $v);
				$array = [];
				foreach ($list as $row) {
					if (isset($agent_move[$row['id']])) {//代理移动过
						foreach ($agent_move[$row['id']] as $move) {
							$last = str_replace('-', '', explode(' ', $move['C_DateTime'])[0]);
							if($last >= $k){
								$row['parent_id'] = $move['BeforeAgentID'];
								xn_log($row['id'].' '.$last.' '.$k.' '.$move['BeforeAgentID'],'move');
							}
						}
					}
					$array[] = $row;
				}
				$ymd = $this->Kv->read('agent_' . $k);
				if (!$ymd) {
					$this->Kv->insert('agent_' . $k, $array);
				}else{
					$this->Kv->update('agent_' . $k, $array);
				}
		}
		$this->response('0000');
	}


}
