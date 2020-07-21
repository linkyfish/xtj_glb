<?php

namespace Payment\Interfaces;

/**
 * Class Order
 *
 * @property \Ctrl\Controller $app
 *
 */
class Order
{
	public $NotifyUrl;
	public $ReturnUrl;
	public $FailUrl;
	public $app;
	public $AppUrl;

	public function __construct($app)
	{
		$this->app = $app;
		$PlatformSetting = $this->app->PlatformSetting->CacheGet('PlatformSetting');
		if (empty($PlatformSetting)) {
			$PlatformSetting = $this->app->PlatformSetting->select([], [], 'name,val');
			$this->app->PlatformSetting->CacheSet('PlatformSetting', $PlatformSetting, 600);
		}
		foreach ($PlatformSetting as $v) {
			$_ENV['conf'][$v['name']] = $v['val'];
		}
	}

	public function create($usrid, $money, $Type, $config)
	{
		$order = [
			'UsrID' => $usrid,
			'Money' => $money,
			'OrderNo' => "No{$Type}" . date('YmdHis') . get_uniqid(8),
			'CreateAT' => time(),
			'Type' => $Type,
			'Status' => 0,
			'PayID' => $config['ID'],
		];
		$order['ID'] = $this->app->Order->insert($order);
		$this->NotifyUrl =  _CONF('staticFileUrl') . '/payment/order/notify/' . $config['ID'] . '_' . $order['ID'];
		$this->ReturnUrl =  _CONF('staticFileUrl') . '/payment/order/ratify/' . xn_encrypt($config['ID'] . '_' . $order['ID']);
		$this->FailUrl =  _CONF('staticFileUrl') . '/payment/order/fail/' . xn_encrypt($config['ID'] . '_' . $order['ID']);
		return $order;
	}


	/**
	 * Notify
	 *
	 * @auth  true
	 * @login true
	 * @menu  false
	 *
	 * @param $order
	 * @param $data
	 * @param $config
	 * 现在三方多数是可以用户自行修改金额--所以需要处理
	 * RealMoney
	 */
	public function Notify($order, $data, $config)
	{
		$r = $this->app->Gamebilog->read(['UsrID' => $order['UsrID'], 'Type' => 61, 'AddTime' => ['>=' => strtotime(date('Y-m-d 00:00:00'))]]);
		$needSong = false;
		if (empty($r['UsrID'])) {
			$needSong = true;
		}
		$usr = $this->app->Usr->read(['usrid' => $order['UsrID']]);

		$this->app->Gamebilog->Add($order['RealMoney'] * 0.01 * $this->app->bl, 61, $order['UsrID'], $usr['agent_id'], $this->app->request->get_client_ip(1), time());
		$this->app->Order->update(['ID' => $order['ID']], ['PayAT' => time(), 'Status' => 1, 'RealMoney' => $order['RealMoney']]);
		if ($needSong) {
			if ($order['RealMoney'] >= 10000) {
				$this->app->Gamebilog->Add(8 * $this->app->bl, 17, $order['UsrID'], $usr['agent_id'], $this->app->request->get_client_ip(1), time());
			}
		}
	}

	public function api($api, $data, $header = [], $i = 1)
	{

		list($https, , $host) = explode('/', $this->AppUrl);
		list($host, $port) = explode(':', $host);
		$https = $https == 'https:' ? true : false;
		if (!$port) {
			$port = $https ? 443 : 80;
		}
		$cli = new \Swoole\Coroutine\Http\Client($host, $port, $https);
		$header['Host'] = $host;
		$cli->setHeaders($header);
		$cli->set(['timeout' => 360]);
		$cli->post($api, $data);
		$errcode = [110 => true, 111 => true];
		if (!empty($errcode[$cli->errCode])) {
			$i += 1;
			$cli->close();
			if ($i < 5) {
				\co::sleep(1);
				return $this->api($api, $data, $header, $i);
			} else {
				xn_log($this->AppUrl . $api . ' ' . xn_json_encode($data), 'payapi_error');
				return false;
			}
		}
		$edata = xn_json_decode($cli->body);
		xn_log($this->AppUrl . $api . ' ' . xn_json_encode($data) . ' ' . $cli->body, 'payapi');
		$cli->close();
		return $edata;
	}

	public function apiHtml($api, $data, $header = [], $i = 1)
	{

		list($https, , $host) = explode('/', $this->AppUrl);
		list($host, $port) = explode(':', $host);
		$https = $https == 'https:' ? true : false;
		if (!$port) {
			$port = $https ? 443 : 80;
		}
		$cli = new \Swoole\Coroutine\Http\Client($host, $port, $https);
		$header['Host'] = $host;
		$cli->setHeaders($header);
		$cli->set(['timeout' => 360]);
		$cli->post($api, $data);
		$errcode = [110 => true, 111 => true];
		if (!empty($errcode[$cli->errCode])) {
			$i += 1;
			$cli->close();
			if ($i < 5) {
				\co::sleep(1);
				return $this->api($api, $data, $header, $i);
			} else {
				xn_log($this->AppUrl . $api . ' ' . xn_json_encode($data), 'payapi_error');
				return false;
			}
		}
		$edata = $cli->body;
		xn_log($this->AppUrl . $api . ' ' . $data . ' ' . $cli->body, 'payapi');
		$cli->close();
		return $edata;
	}

}