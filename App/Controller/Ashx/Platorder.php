<?php
/**
 * Created by PhpStorm.
 * User: yinhanmin
 * Date: 2019-03-09
 * Time: 08:49
 */

namespace Ashx;


use Cash\Interfaces\Order;
use Ctrl\GameController;

class Platorder extends GameController
{


	public function TransferOrder_GetTransferNoticeLog()
	{
		$data['TotalNum'] = 0;
		$this->response('0000', $data);
	}

	public function accountCashOrder_QueryCashOrder()
	{

		$this->needadmin();
		$QueryID = $this->request->param('QueryID', -1);
		$RepayID = $this->request->param('RepayID', -1);
		$user = $this->request->param('user', '');
		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');
		$pageIndex = $this->request->param('pageIndex', 1);
		$pageSize = $this->request->param('pageSize', 50);
		$cond = [];

		$QueryID != -1 AND $cond['Status'] = $QueryID;
		$RepayID != -1 AND $cond['RepayID'] = $RepayID;
		if (!empty($user)) {
			$player = $this->findPlayer($user);
			$cond['UsrID'] = $player['usrid'];
		}
		if ($sDate) {
			$cond['AddTime'] = ['>=' => strtotime($sDate), '<=' => strtotime($eDate)];
		} else {
			$cond['AddTime'] = ['>=' => strtotime(date('Y-m-d 00:00:00')), '<=' => time()];
		}

		$list = $this->CashLog->select($cond, ['LogID' => -1], '*', $pageIndex, $pageSize);
		$UsrID = arrlist_values($list, 'UsrID');
		$users = arrlist_key_values($this->Usr->select(['usrid' => $UsrID], [], 'usrid,openid'), 'usrid', 'openid');
		$data['results'] = [];
		$start = ($pageIndex - 1) * $pageSize;
		$admins = $this->agent_id_name;
		foreach ($list as $k => $v) {
			$vv['Rownum'] = $start + $k + 1;
			$vv['ID'] = $v['LogID'];
			$vv['Account'] = !empty($admins[$v['AdminID']]) ? $admins[$v['AdminID']] : '-';
			$vv['OperationType'] = $v['Type'];//
			$vv['OrderStatus'] = intval($v['Status']);//
			$vv['UserID'] = $v['UsrID'];//
			$vv['UserName'] = !empty($users[$v['UsrID']]) ? $users[$v['UsrID']] : '-';;//
			$vv['SafeCode'] = 'N/A';//
			$vv['RepayID'] = $v['RepayID'];//
			$vv['Is_Df'] = $v['Is_Df'];//
			$vv['CashNum'] = calculate($v['Number']);//
			$vv['RealCashNum'] = calculate($v['Money']);//
			$vv['ServiceFee'] = calculate($v['Comm']);//手续费
			$vv['C_DateTime'] = date('Y-m-d H:i:s', $v['AddTime']);//
			$vv['BankName'] = $v['BankName'];//
			$vv['BankNum'] = $v['BankCard'];//
			$vv['BankOwner'] = $v['BankUser'];//
			$vv['BeforeMoney'] = calculate($v['BeforeNumber']);//
			$vv['AfterMoney'] = calculate($v['AfterNumber']);//
			$vv['Sup'] = 1;//
			$vv['BankUniCode'] = 'N/A';//
			$vv['Memo'] = 'N/A';//
			$vv['OrderID'] = $v['OrderID'];//
			$data['results'][] = $vv;
		}

		$data['total'] = $this->CashLog->count($cond);
		$cond['Status'] = 20;
		$data['total_SuccessSum'] = calculate($this->CashLog->sum('Number', $cond));
		$cond['Is_Df'] = 1;
		$data['total_DaifuSum'] = calculate($this->CashLog->sum('Money', $cond));
		unset($cond['Is_Df']);
		$data['total_ServiceSum'] = calculate($this->CashLog->sum('Comm', $cond));

		$cond['Status'] = 10;
		$data['total_FailData'] = $this->CashLog->count($cond);
		$cond['Status'] = 40;
		$data['total_FailSum'] = $this->CashLog->count($cond);
		$this->response('0000', $data);
	}

	public function accountCashOrder_Report_CashOrder()
	{
		$this->needadmin();
		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');
		if ($sDate) {
			$cond['AddTime'] = ['>=' => strtotime($sDate), '<=' => strtotime($eDate)];
		} else {
			$cond['AddTime'] = ['>=' => strtotime(date('Y-m-d 00:00:00')), '<=' => time()];
		}
		$data['results'] = $this->CashLog->sum_by_sub($cond, $this->agent_id_name);
		$this->response('0000', $data);
	}


	public function paymentOrder_paymentOrder()
	{
		$this->needadmin();
		$user = $this->request->param('user', '');
		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');
		$QueryID = $this->request->param('QueryID', '');
		$PlatformID = $this->request->param('PlatformID', '');
		$ShangHuListID = $this->request->param('ShangHuListID', 0);
		$PayType = $this->request->param('PayType', 0);
		$pageIndex = $this->request->param('pageIndex', 1);
		$pageSize = $this->request->param('pageSize', 50);
		if (empty($sDate)) {
			$sDate = strtotime(date('Y-m-d 00:00:00'));
			$eDate = strtotime(date('Y-m-d H:i:s'));
		} else {
			$sDate = strtotime($sDate);
			$eDate = strtotime($eDate);
		}

		$cond = [];
		$QueryID != -1 AND $cond['Status'] = $QueryID;
		$ShangHuListID > 0 AND $cond['PayID'] = $ShangHuListID;
		if (!empty($user)) {
			$user = $this->findPlayer($user);
			$cond['UsrID'] = $user['usrid'];
		}

		$PayType > 0 AND $cond['Type'] = $PayType;
		$cond['CreateAT'] = ['>=' => $sDate, '<=' => $eDate];
		//$QueryID != -1 AND $cond['IsNote'] = $QueryID;
		//ZDD 总订单
		//ZCC 总成功数量
		//ZJE 总成功金额
		//SGDD 手工订单
		//SGDDCC 手工成功订单数量
		//SGJE 手工成功金额

		//$data = $this->Order->read($cond, '*');
		$list = $this->Order->select($cond, ['ID' => -1], '*', $pageIndex, $pageSize);
		$start = ($pageIndex - 1) * $pageSize;
		$total = $this->Order->count($cond);
		$userlist = [];
		if ($list) {
			$uids = arrlist_values($list, 'UsrID');
			$userlist = $this->Usr->select(['usrid' => $uids], [], 'usrid,openid');
			$userlist = arrlist_key_values($userlist, 'usrid', 'openid');
		}
		$data = [];
		foreach ($list as $k => &$v) {
			$v['Rownum'] = $start + $k + 1;
			//$v['CostNum'] = $v['PayMoney'];
			if ($this->isadmin && $this->roleInfo['dh']) {
				$v['Account'] = $userlist[$v['UsrID']] . '(' . $v['UsrID'] . ')';
			} else {
				$v['Account'] = $v['UsrID'];
			}
			$v['payChannel'] = 1;

			$v['C_DateTime'] = date('Y-m-d H:i:s', $v['CreateAT']);
			//$v['Type'] = 2; // $typeMap[$data['PayType']];

			$v['RealMoney'] = bcmul($v['RealMoney'], 0.01, 2);
			$v['ActionID'] = $v['IsAction'];
			$v['CostNum'] = bcmul($v['Money'], 0.01, 2);
		}

		$this->Order->update(['Status' => 0, 'CreateAT' => ['<=' => time() - 7200]], ['Status' => 3]);

		$cond['Status'] = [0, 2, 3];
		$data['total_FailSum'] = $this->Order->sum('Money', $cond);

		$cond['Status'] = 1;

		$data['total_SuccessSum'] = $this->Order->sum('RealMoney', $cond);
		$data['total_FailData'] = $total - $this->Order->count($cond);

		$data['results'] = $list;
		$data['total'] = $total;
		$data['totalSumData'] = $total;
		$data['total_FailSum'] = bcmul($data['total_FailSum'], 0.01, 2);
		$data['total_SuccessSum'] = bcmul($data['total_SuccessSum'], 0.01, 2);
		$this->response('0000', $data);
	}

	public function paymentOrder_addOrder()
	{
		$this->needadmin();
		$user = $this->request->param('user', '');
		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');
		$adminid = $this->request->param('adminid', 0);;
		$pageIndex = $this->request->param('pageIndex', 1);
		$pageSize = $this->request->param('pageSize', 50);
		if (empty($sDate)) {
			$sDate = strtotime(date('Y-m-d 00:00:00'));
			$eDate = strtotime(date('Y-m-d H:i:s'));
		} else {
			$sDate = strtotime($sDate);
			$eDate = strtotime($eDate);
		}

		$cond = [];

		if (!empty($user)) {
			$user = $this->findPlayer($user);
			$cond['usrid'] = $user['usrid'];
		}
		if ($this->token['RoleID'] > 2) {
			$cond['action_id'] = $this->token['id'];
		} else {
			$adminid && $cond['action_id'] = $adminid;
		}
		$cond['style'] = [51, 52];
		$cond['times'] = ['>=' => date('Y-m-d H:i:s', $sDate), '<=' => date('Y-m-d H:i:s', $eDate)];

		$list = $this->MoneyTrans->select($cond, ['id' => -1], '*', $pageIndex, $pageSize);
		$start = ($pageIndex - 1) * $pageSize;
		$total = $this->MoneyTrans->count($cond);
		$userlist = [];
		if ($list) {
			$uids = arrlist_values($list, 'usrid');
			$userlist = $this->Usr->select(['usrid' => $uids], [], 'usrid,openid');
			$userlist = arrlist_key_values($userlist, 'usrid', 'openid');
		}
		$agents = $this->agent_id_name;
		$data = [];
		foreach ($list as $k => &$v) {
			$v['Rownum'] = $start + $k + 1;
			if ($this->isadmin && $this->roleInfo['dh']) {
				$v['Account'] = $userlist[$v['usrid']] . '(' . $v['usrid'] . ')';
			} else {
				$v['Account'] = $v['usrid'];
			}
			$v['AddTime'] = $v['times'];
			$v['Money'] = calculate($v['chips']);
			$v['AdminName'] = $agents[$v['action_id']];
			$v['IP'] = long2ip($v['IP']);
		}

		$data['results'] = $list;
		$data['total'] = $total;
		$this->response('0000', $data);
	}


	public function paymentOrder_error_log()
	{
		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');
		$logs = $this->MoneyTrans->select(['times' => ['>=' => $sDate, '<=' => $eDate]], [], 'id,usrid,chips,diamond,times,style,transtype,gameID,tableID,BeginBlance,EndBlance,gameNumb');
		$logs = arrlist_group($logs, 'usrid');
		$data['results'] = [];
		foreach ($logs as $usrid => $row) {
			$list = $this->MoneyTrans->GameList([], 1, 1000000, $row);
			$cc = 0;
			foreach ($list['results'] as $v) {
				!$cc && $cc = $v['EndBlance'];
				$r = bcadd(($v['EndBlance'] - $v['BeginBlance']) - ($v['TotalWin'] - $v['TotalBet']), 0, 2);
				if ($v['EndBlance'] != $cc) {
					$data['results'][] = ['usrid' => $usrid, 'm' => $cc . '~' . $v['BeginBlance'] . '~' . $v['EndBlance'], 'times' => $v['PlayTime']];
				} else if ($r > 0.02 || $r < -0.02) {
					$data['results'][] = ['usrid' => $usrid, 'm' => $r . ' ~ ' . bcadd($v['TotalWin'] - $v['TotalBet'], 0, 2) . ' ~ ' . bcadd($v['EndBlance'] - $v['BeginBlance'], 0, 2), 'times' => $v['PlayTime']];
				}
				$cc = $v['BeginBlance'];
			}
		}

		$this->response('0000', $data);
	}


	public function paymentOrder_gamelog()
	{
		$this->needadmin(1);
		$user = $this->request->param('user', '');
		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');
		$pageIndex = $this->request->param('pageIndex', 1);
		$pageSize = $this->request->param('pageSize', 500);
		if (empty($sDate)) {
			$sDate = date('Y-m-d 00:00:00');
			$eDate = date('Y-m-d H:i:s');
		}
		if (!empty($user)) {
			$user = $this->findPlayer($user);
			$cond['usrid'] = $user['usrid'];
		}

		$cond['times'] = ['>=' => $sDate, '<=' => $eDate];
		$data['results'] = $this->MoneyTrans->select($cond, ['times' => -1, 'id' => -1], '*', $pageIndex, $pageSize);
		$data['total'] = $this->MoneyTrans->count($cond);
		$this->response('0000', $data);
	}


	public function paymentOrder_winuser()
	{
		$this->needadmin(1);
		$user = $this->request->param('user', '');
		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');

		$minMoney = $this->request->param('minMoney', '1');

		$maxMoney = $this->request->param('maxMoney', '1000');

		$pageIndex = $this->request->param('pageIndex', 1);
		$pageSize = $this->request->param('pageSize', 200);
		if (empty($sDate)) {
			$sDate = date('Y-m-d 00:00:00');
			$eDate = date('Y-m-d H:i:s');
		}
		if (!empty($user)) {
			$user = $this->findPlayer($user);
			$cond['usrid'] = $user['usrid'];
		}

		$cond['times'] = ['>=' => $sDate, '<=' => $eDate];


		$cond['minMoney'] = ['>=' => $minMoney, '<=' => $maxMoney];

		//$cond['times'] = ['>=' => $sDate, '<=' => $eDate];

		$cond['style'] = [51, 52, 61, 37];
		$cond['times'] = ['>=' => $sDate, '<=' => $eDate];
		//$data['results'] = $this->MoneyTrans->select($cond,['times'=>-1,'id'=>-1],'*',$pageIndex,$pageSize);
		$data['results'] = $this->MoneyTrans->select($cond, ['times' => -1, 'id' => -1], 'id,usrid ,sum(chips) as tixian ', $pageIndex, $pageSize, '', 'group by usrid');
		$data['total'] = $this->MoneyTrans->count($cond);
		$this->response('0000', $data);
	}


	public function paymentOrder_gamebilog()
	{
		$this->needadmin(1);
		$user = $this->request->param('user', '');
		$sDate = $this->request->param('sDate', '');
		$eDate = $this->request->param('eDate', '');
		$pageIndex = $this->request->param('pageIndex', 1);
		$pageSize = $this->request->param('pageSize', 500);
		if (empty($sDate)) {
			$sDate = strtotime(date('Y-m-d 00:00:00'));
			$eDate = strtotime(date('Y-m-d H:i:s'));
		} else {
			$sDate = strtotime($sDate);
			$eDate = strtotime($eDate);
		}
		if (!empty($user)) {
			$user = $this->findPlayer($user);
			$cond['UsrID'] = $user['usrid'];
			$_cond['usrid'] = $user['usrid'];
		}
		$cond['AddTime'] = ['>=' => $sDate, '<=' => $eDate];
		$_cond['times'] = ['>=' => date('Y-m-d H:i:s', $sDate), '<=' => date('Y-m-d H:i:s', $eDate)];
		$data['results'] = $this->Gamebilog->select($cond, ['LogID' => -1], '*', $pageIndex, $pageSize);
		$data['total'] = $this->Gamebilog->count($cond);
		$_cond['style'] = array_keys($this->MoneyTrans->Type);
		$moneys = $this->MoneyTrans->select($_cond);
		$data['results'] = arrlist_multisort($data['results'], 'AddTime', false);
		$moneys = arrlist_multisort($moneys, 'times', false);
		$moneys = arrlist_group($moneys, 'usrid');
		foreach ($data['results'] as &$row) {
			$row['AddTime'] = date('Y-m-d H:i:s', $row['AddTime']);
			$row['info'] = [];
			$row['ok'] = 0;
			foreach ($moneys[$row['UsrID']] as $k => $v) {
				if ($v['style'] == $row['Type']) {
					if ($v['chips'] == $row['Money']) {
						if ($row['AddTime'] == $v['times']) {
							$row['info'] = ['* ' . $v['chips'] . ' ' . $v['times']];
							$row['ok'] = 1;
							break;
						} else if ((strtotime($row['AddTime']) - strtotime($v['times']) > 0 && strtotime($row['AddTime']) - strtotime($v['times']) < 120) || $row['Type'] == 36 || $row['Type'] == 50) {
							$row['info'][] = $v['chips'] . ' ' . $v['times'] . ' *';
						}
					} else {
						//$row['info'][]=$v['chips'].' '.$v['times'];
					}
				}
			}
			$row['info'] = implode('<br>', $row['info']);
			$row['Type'] = $this->MoneyTrans->Type[$row['Type']];
		}
		$this->response('0000', $data);
	}

	/**
	 * 进分补单
	 */


	public function paymentOrder_onOrderSetScore()
	{
		$this->needadmin();
		$orderId = $this->request->param('ID');
		$orderRes = $this->Order->read(['ID' => $orderId]);
		$player = $this->Usr->read(['usrid' => $orderRes['UsrID']]);
		$money = $orderRes['Money'];
		$Status = $orderRes['Status'];
		if ($orderRes['Status'] == 0 || $orderRes['Status'] == 3) {
			$this->Order->update(['ID' => $orderId], ['Status' => 1, 'IsAction' => 1, 'AdminID' => $this->token['id'], 'RealMoney' => $money, 'ActionAT' => time(), 'PayAT' => time()]);
			$this->Gamebilog->Add($money * 0.01 * $this->bl, 61, $player['usrid'], $player['agent_id'], $this->request->get_client_ip(1), time(), $this->token['id']);
			$this->response('0000');
		} else {
			$this->response('0001', [], '此订单无法操作补单');
		}
	}

	public function paymentOrder_getSumMoney()
	{

		$this->response('0000', ['sum' => 0]);

	}

	public function transferOrder()
	{
		$this->response('0000', ['results' => []]);
	}

	public function accountCashOrder_GetUserOrderInfo()
	{

		$this->needadmin();
		$id = $this->request->param('DataID', '');
		$_data = $this->CashLog->read(['LogID' => $id], 'BankUser as BankOwner,Money as RealCashNum,Comm as ServiceFee,BankCard as BankNum,BankName,BankAddress,Status as OrderStatus');
		$_data['ServiceFee'] = calculate($_data['ServiceFee']);
		$_data['RealCashNum'] = calculate($_data['RealCashNum']);
		$_data['BankName'] = $_data['BankAddress'] . '' . $_data['BankName'];
		$_data['BankNum'] = str_replace(" ", "", $_data["BankNum"]);
		$data['results'] = [$_data];
		$this->response('0000', $data);
	}

	public function accountCashOrder_DoCashPass()
	{

		$this->needadmin();
		$id = $this->request->param('DataID', '');
		$cache = $this->CashLog->CacheGet('cash_' . $id);
		if (!empty($cache)) {
			$this->response('0001', [], '操作频繁');
		}
		$this->CashLog->CacheSet('cash_' . $id, time(), 30);
		$config = $this->RepayConfig->read(['is_def' => 1]);
		empty($config['ID']) && $this->response('0001', [], '代付未开启');
		$_data = $this->CashLog->read(['LogID' => $id]);
		empty($_data['LogID']) AND $this->response('0001', [], '订单不存在');
		$_data['Status'] != 10 AND $this->response('0001', [], '订单已处理');
		$usr = $this->Usr->read(['usrid' => $_data['UsrID']]);
		$this->hspower($usr['usrid'], 1);
		$this->CashLog->update(['LogID' => $id], ['Status' => 50, 'AdminID' => $this->token['id'],'RepayID'=>$config['ID'], 'DoTime' => time(), 'Is_Df' => 1]);
		include_once _include(__APPDIR__ . 'Controller/Cash/Interfaces/Order.php');
		$cash = new Order($this);
		$cash->create($_data, $config, $this->token['id'], $this->request->get_client_ip(1));
		$this->response('0001', json_encode($cash));
	}

	public function accountCashOrder_GetUserOrderInfo2()
	{

		$this->needadmin();
		$id = $this->request->param('DataID', '');
		$_data = $this->CashLog->read(['LogID' => $id], 'BankUser as BankOwner,Money as RealCashNum,Comm as ServiceFee,BankCard as BankNum,BankName,Status as OrderStatus');
		$_data['ServiceFee'] = calculate($_data['ServiceFee']);
		$_data['RealCashNum'] = calculate($_data['RealCashNum']);
		$_data['BankNum'] = str_replace(" ", "", $_data["BankNum"]);
		$data['results'] = [$_data];
		$this->response('0000', $data);
	}

	public function accountCashOrder_DoRenGongPass()
	{
		$this->needadmin();
		$id = $this->request->param('DataID', '');
		$cache = $this->CashLog->CacheGet('cash_' . $id);
		if (!empty($cache)) {
			$this->response('0001', [], '操作频繁');
		}
		$this->CashLog->CacheSet('cash_' . $id, time(), 30);
		$_data = $this->CashLog->read(['LogID' => $id]);
		empty($_data['LogID']) AND $this->response('0001', [], '订单不存在');
		$_data['Status'] != 10 AND $this->response('0001', [], '订单已处理');
		$usr = $this->Usr->read(['usrid' => $_data['UsrID']]);
		$this->hspower($usr['usrid'], 1);
		$this->CashLog->update(['LogID' => $id], ['Status' => 20, 'AdminID' => $this->token['id'], 'DoTime' => time()]);
		$this->MoneyTrans->update(['usrid' => $_data['UsrID'], 'chips' => -$_data['Number'], 'BeginBlance' => $_data['BeforeNumber'], 'action_id' => 0, 'style' => 37], ['action_id' => $this->token['id'], 'action_time' => time(), 'action_ip' => $this->request->get_client_ip(1)]);
		$this->OperateLog->Add($this->token['id'], $_data['UsrID'], 0, $this->request->get_client_ip(1), '人工打款(' . $_data['UsrID'] . '):' . calculate($_data['Number']));
		$this->response('0000');
	}

	/**
	 * 禁用提现
	 * accountCashOrder_DoDisCashOrder
	 *
	 * @auth  true
	 * @login true
	 * @menu  false
	 * @throws \Exception
	 */
	public function accountCashOrder_DoDisCashOrder()
	{
		$this->needadmin();
		$id = $this->request->param('DataID', '');
		$cache = $this->CashLog->CacheGet('cash_' . $id);
		if (!empty($cache)) {
			$this->response('0001', [], '操作频繁');
		}
		$this->CashLog->CacheSet('cash_' . $id, time(), 30);

		$_data = $this->CashLog->read(['LogID' => $id]);
		empty($_data['LogID']) AND $this->response('0001', [], '订单不存在');
		$_data['Status'] != 10 AND $this->response('0001', [], '订单已处理');
		//不允许操作盘口方的东西
		$usr = $this->Usr->read(['usrid' => $_data['UsrID']]);
		$this->hspower($usr['usrid'], 1);
		$this->CashLog->update(['LogID' => $id], ['Status' => 30, 'AdminID' => $this->token['id'], 'DoTime' => time()]);
		$Chips = $usr['chips'] + $usr['bankchips'];
		$content = sprintf("【%s】" . $this->MoneyTrans->Type[$_data['Type']] . ":金额:%s", $_data['UsrID'], calculate($Chips));
		$this->OperateLog->Add($this->token['id'], $_data['UsrID'], 0, $this->request->get_client_ip(1), $content);
		$this->response('0000');

	}

	/**
	 * 取消订单
	 * accountCashOrder_DoCancelCashOrder
	 *
	 * @auth  true
	 * @login true
	 * @menu  false
	 */

	public function accountCashOrder_DoCancelCashOrder()
	{
		$this->needadmin();
		$id = $this->request->param('DataID', '');
		$cache = $this->CashLog->CacheGet('cash_' . $id);
		if (!empty($cache)) {
			$this->response('0001', [], '操作频繁');
		}
		$this->CashLog->CacheSet('cash_' . $id, time(), 30);
		$_data = $this->CashLog->read(['LogID' => $id]);
		empty($_data['LogID']) AND $this->response('0001', [], '订单不存在');
		$_data['Status'] != 10 AND $this->response('0001', [], '订单已处理');
		//不允许操作盘口方的东西
		$usr = $this->Usr->read(['usrid' => $_data['UsrID']]);
		$this->hspower($usr['usrid'], 1);
		$this->CashLog->update(['LogID' => $id], ['Status' => 40, 'AdminID' => $this->token['id'], 'DoTime' => time()]);
		$this->Gamebilog->Add($_data['Number'], 38, $_data['UsrID'], $_data['AgentID'], $this->request->get_client_ip(1), time(), $this->token['id']);
		$this->response('0000');
	}


}