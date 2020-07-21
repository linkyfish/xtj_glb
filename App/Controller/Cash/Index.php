<?php

namespace Cash;

use Ctrl\Controller;

// hook cash_index_use.php

Class Index extends Controller
{

//    public function __construct($server, $route, \Request $request, $response)
//    {
//        parent::__construct($server, $route, $request, $response);
//        $PlatformSetting = $this->PlatformSetting->CacheGet('PlatformSetting');
//        if (empty($PlatformSetting)) {
//            $PlatformSetting = $this->PlatformSetting->select([], [], 'name,val');
//            $this->PlatformSetting->CacheSet('PlatformSetting', $PlatformSetting, 600);
//        }
//        foreach ($PlatformSetting as $v) {
//            $_ENV['conf'][$v['name']] = $v['val'];
//        }
//
//    }


	// hook cash_index_start.php
	public function Index()
	{
		$xcode = '';
		$url = 'https://'.$this->request->_S('HOST');
		$ua = strtolower($this->request->_S('USER-AGENT'));
		if (strpos($ua, 'micromessenger') !== false) {//weixin
			return $this->view(get_defined_vars(),'Index.Jump');
		} elseif (strpos($ua, 'qqtheme') !== false) {
			return $this->view(get_defined_vars(),'Index.Jump');
		}else{
			return $this->view(get_defined_vars());
		}
		//return $this->View(get_defined_vars());
	}

	public function Index_Confirm()
	{

		$account = $this->request->param('account', '');
		$safecode = $this->request->param('safecode', '');
		$money = $this->request->param('money', 0);
		$bankcard = trim($this->request->param('bankNo', ''));
		$realname = trim($this->request->param('bankName', ''));
		$bankSon = trim($this->request->param('bankSon', ''));
		$bankAddress = trim($this->request->param('bankAddress', ''));
		$this->CheckEmpty([$account, $safecode, $money, $bankcard, $realname, $bankSon, $bankAddress], ['游戏账户', '安全码', '提现金额', '卡号', '持卡人', '开户行名称', '开户行信息']);

		$player = $this->Account->read_one(['account' => $account]);

		if (empty($player) || $player['safecode'] != $safecode) {
			$this->response('0001', [], '账户或安全码有误');
		}
		if ($player['safecode'] == '123456') {
			$this->response('0001', [], '请先进入游戏设置安全码');
		}
		$chips = calculate($player['chips']);
		if ($money < 100 || $money % 50 > 0) {
			$this->response('0001', [], '金额小于100或不是50的倍数');
		}
		if ($money > $chips) {
			$this->response('0001', [], '余额不足');
		}
		$user = $this->Usr->read(['usrid' => $player['usrid']]);
		if (empty($user['realname'])) {
			$this->response('0001', [], '请先在大厅设置用户真实姓名再继续操作');
		}
		$player['online'] > 0 AND $this->response('0001', [], '请离线后继续操作！');
		$player['chips'] < -$money AND $this->response('0001', [], '余额不足');
		$updata = [];

		$updata['bankcard'] = $bankcard;
		$updata['bankSon'] = $bankSon;
		$updata['bankAddress'] = $bankAddress;
		trim($player['realname']) != trim($realname) && $this->response('0001', [], '真实姓名不一致');

		$time = time();
		$key = 'cash_lock_' . $player['usrid'];
		$islock = $this->User->CacheGet($key);
		if (!empty($islock['time'])) {
			$this->response('0001', [], '操作太快');
		}
		$this->User->CacheSet($key, ['time' => $time], 60);

		$Rate = _CONF('CashRate', 3);
		$updata && $this->Usr->update(['usrid' => $player['usrid']], $updata);
		$rUp = $this->Gamebilog->Sub(-$money * $this->bl, 37, $player['usrid'], $player['agent_id'], $this->request->get_client_ip(1), time(), 0);
		if ($rUp['success']) {
			$user = $this->Usr->read(['usrid' => $player['usrid']]);
			$insert = [
				'UsrID' => $player['usrid'],
				'BeforeNumber' => $user['chips'] + ($money * $this->bl),
				'Number' => $money * $this->bl,
				'AfterNumber' => $user['chips'],
				'Money' => $money * $this->bl * (1 - $Rate * 0.01),
				'AddTime' => time(),
				'Status' => 10,
				'Rate' => $Rate,
				'BankCard' => $bankcard,
				'BankName' => $bankSon,
				'BankUser' => $player['realname'],
				'BankAddress' => $bankAddress,
				'AgentID' => $player['agent_id'],
				'IP' => $this->request->get_client_ip(1),
				'Comm' => $money * $this->bl * ($Rate * 0.01),
				'OrderID' => "C" . date('YmdHis') . mt_rand(1000, 9999),
			];
			$this->CashLog->insert($insert);
			$this->User->CacheDel($key);
			$this->response('0000', []);
		} else {
			for($i=1;$i<=30;$i++){
				\co::sleep(1);
				$r = $this->Gamebilog->read(['LogID'=>$rUp['LogID']]);
				if($r['Status']==1){
					$user = $this->Usr->read(['usrid' => $player['usrid']]);
					$insert = [
						'UsrID' => $player['usrid'],
						'BeforeNumber' => $user['chips'] + ($money * $this->bl),
						'Number' => $money * $this->bl,
						'AfterNumber' => $user['chips'],
						'Money' => $money * $this->bl * (1 - $Rate * 0.01),
						'AddTime' => time(),
						'Status' => 10,
						'Rate' => $Rate,
						'BankCard' => $bankcard,
						'BankName' => $bankSon,
						'BankUser' => $player['realname'],
						'BankAddress' => $bankAddress,
						'AgentID' => $player['agent_id'],
						'IP' => $this->request->get_client_ip(1),
						'Comm' => $money * $this->bl * ($Rate * 0.01),
						'OrderID' => "C" . date('YmdHis') . mt_rand(1000, 9999),
					];
					$this->CashLog->insert($insert);
					$this->User->CacheDel($key);
					$this->response('0000', []);
				}
			}

			$rUp['LogID'] && $this->Gamebilog->update(['LogID'=>$rUp['LogID'],'Status'=>0],['Status'=>2]) && xn_log($rUp['LogID'],'GamebilogDelete');
			$this->response('0001', [], $rUp['msg']);
		}
	}

	public function Index_POST()
	{
		$account = $this->request->param('account', '');
		$safecode = $this->request->param('safecode', '');
		$this->CheckEmpty([$account, $safecode], ['游戏账户', '安全码']);
		$player = $this->Account->read_one(['account' => $account]);

		if (empty($player) || $player['safecode'] != $safecode) {
			$this->response('0001', [], '账户或安全码有误');
		}

		if ($player['safecode'] == '123456') {
			$this->response('0001', [], '请先进入游戏设置安全码');
		}
		$data['chips'] = calculate($player['chips']);
		$data['bankcard'] = $player['bankcard'];
		$data['bankSon'] = $player['bankSon'];
		$data['bankAddress'] = $player['bankAddress'];

		$data['bankUser'] = (is_null($player['realname']) || empty($player['realname'])) ? '' : $player['realname'];
		$this->response('0000', $data);
	}

	public function Pay(){

	}

	public function Notify()
	{
		include_once _include(__APPDIR__ . 'Controller/Cash/Interfaces/Order.php');
		$data = $this->request->param();
		$data['rawContent']= $this->request->rawContent();
		xn_log($this->request->_S('HOST').' '.$this->request->get_client_ip().' 回调时间  '.date('Y-m-d H:i:s',time()).' '.json_encode($data).'raw '.$this->request->rawContent(), 'cash_notify');
		list($payment_id,$order_id) =explode('_', $this->request->param('3',''));
		$config= $this->RepayConfig->read(['ID' => $payment_id]);
		$payment = ucfirst(strtolower($config['Channel']));
		if (is_file(__APPDIR__ . 'Controller/Cash/Interfaces/' . $payment . '.php')) {
			include_once _include(__APPDIR__ . 'Controller/Cash/Interfaces/' . $payment . '.php');
			$order = $this->RepayLog->read(['LogID'=>$order_id]);
			empty($order['LogID']) AND $this->response('0001',[],'订单不存在');
			$contor = '\\Cash\\Interfaces\\' . $payment;
			$pay = new $contor($this);
			return $pay->Notify($order,$data,$config);
		} else {
			$this->response('0001', [], '支付平台等待接入');
		}
	}



	// hook cash_index_end.php

}
