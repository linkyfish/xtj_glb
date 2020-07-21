<?php
namespace Model;
// hook model_gamebilog_use.php

use App\Model;

class GamebilogModel extends Model
{
	// hook model_gamebilog_public_start.php
	public $table = 'gamebilog';
	public $index = 'LogID';
	//public $is_delete = 'is_delete';
	public $Type;
//    public $Type = [
//        25 => '注册送',
//        29 => '申请送',
//        35 => '系统送',
//        36 => '活动送',
//        37 => '申请提现',
//        38 => '取消提现',
//        39 => '禁用提现',
//        50 => '短信送',
//        51 => '手工上分',
//        52 => '手工减分',
//        53 => '手工减银行分',
//        61 => '商城上分',
//        114 => '活动清零'
//    ];

	public function __construct($server)
	{
		parent::__construct($server);
		$this->Type = $this->MoneyTrans->Type;
	}
	// hook model_gamebilog_public_end.php

	// hook model_gamebilog_start.php

	public function Add($Money, $Type, $Usrid, $AgentID, $IP, $DoAt, $AdminID = 0)
	{

		$user = $this->Usr->read(['usrid' => $Usrid]);
		$Chips = $user['chips'] + $user['bankchips'];
		if ($DoAt) {
			$user = $this->Usr->read(['usrid' => $Usrid]);
			$Chips = $user['chips'] + $user['bankchips'];
			$insert = [
				'Type' => $Type,
				'Money' => $Money,
				'AfterMoney' => $Chips,
				'AddTime' => time(),
				'UsrID' => $Usrid,
				'AdminID' => $AdminID,
				'AgentID' => $AgentID,
				'IP' => $IP,
				'DoAt' => $DoAt,
				'Status'=>0,
			];
			$orderID = $this->insert($insert);
			if ($orderID) {
				$this->Doa($orderID);
			}
		} else {
			$insert = [
				'Type' => $Type,
				'Money' => $Money,
				'AfterMoney' => $Chips + $Money,
				'AddTime' => time(),
				'UsrID' => $Usrid,
				'AdminID' => $AdminID,
				'AgentID' => $AgentID,
				'IP' => $IP,
				'DoAt' => $DoAt,
				'Status'=>0,
			];

			if ($AdminID) {
				$content = sprintf("【%s】" . $this->Type[$Type] . ":未进分", $Usrid);
				$this->OperateLog->Add($AdminID, $Usrid, 0, $IP, $content);
			}
			$orderID = $this->insert($insert);
		}
		return $orderID;
	}


	public function Sub($Money, $Type, $Usrid, $AgentID, $IP, $DoAt, $AdminID, $cashOderId = 0)
	{
		if ($DoAt) {
			$user = $this->Usr->read(['usrid' => $Usrid]);
			$Chips = $user['chips'] + $user['bankchips'];
			$insert = [
				'Type' => $Type,
				'Money' => $Money,
				'AfterMoney' => $Chips,
				'AddTime' => time(),
				'UsrID' => $Usrid,
				'AdminID' => $AdminID,
				'AgentID' => $AgentID,
				'Status'=>0,
				'IP' => $IP,
			];
			$orderID = $this->insert($insert);
			if($orderID){
				$req = $this->Doa($orderID);
				return ['success' =>$req ,'LogID'=>$orderID, 'msg' =>$req==true? '操作成功':"发送提现请求失败,请稍候重试,谢谢！"];
			}
			return ['success' => false, 'msg' => '操作失败'];
		} else {
			$content = sprintf("【%s】" . $this->Type[$Type] . ":未减分", $Usrid);
			$this->OperateLog->Add($AdminID, $Usrid, 0, $IP, $content);
			return ['success' => true, 'msg' => '操作成功'];
		}
	}

	public function Doa($LogID)
	{
		$Log = $this->read(['LogID' => $LogID]);
		$Usrid = $Log['UsrID'];
		$Type = $Log['Type'];
		$Money = $Log['Money'];
		$time = time();
		$user = $this->Usr->read(['usrid' => $Usrid]);
		$formData = [
			'playerid' => $Usrid,
			'orderID' => $LogID,
			'addType' => $Type,
			'Status'=>$Log['Status'],
			'note' => $this->Type[$Type],
		];
		if($Log['AdminID']){
			$formData['action_id'] = $Log['AdminID'];
			$formData['action_time'] = time();
			$formData['action_ip'] = long2ip($Log['IP']);
		}

		$serverInfo = $this->RoomBank->getGameServerInfoById($user['roomid']);
		$update_url = 'http://' . $serverInfo['ip'] . ':' . $serverInfo['port'] . '/AddPlayerScore';
		$response = post_api($update_url, $formData);
		if ($response['success'] == 1) {
			$this->update(['LogID' => $LogID], ['DoAt' => $time]);
			$user = $this->Usr->read(['usrid' => $Usrid]);
			$Chips = $user['chips'] + $user['bankchips'];
			$this->Gamebilog->update(['LogID' => $LogID], ['AfterMoney' => $Chips]);
			if ($Log['AdminID']) {
				$content = sprintf("【%s】" . $this->Type[$Type] . ":".($Money>0?'进分':'减分').":%s", $Usrid, calculate($Money));
				$this->OperateLog->Add($Log['AdminID'], $Usrid, 0, $Log['IP'], $content);
			}
			if ($Money < 0) {
				$this->Usr->update(['usrid' => $Usrid], ['total_cash-' => $Money]);
				\co::sleep(2);
				$formData = [
					'playerid' => $Usrid,
					'addMoney' => $Money,
				];
				$tips_url = 'http://' . _CONF('gameServerIp') . ':9090' . '/PushAlertToClient';
				post_api($tips_url, $formData);
			} else {
				$this->Usr->update(['usrid' => $Usrid], ['total_uppoints+' => $Money]);
			}
			return true;
		} else {
			return false;
		}
	}

	// hook model_gamebilog_end.php
}

?>