<?php

namespace Cash\Interfaces;

/**
 * Class Order
 *
 * @property \Ctrl\Controller $app
 *
 */
class Order
{

	public $app;


	public function __construct($app)
	{
		$this->app = $app;
	}

	public function create($cash,$config,$adminID,$IP)
	{
		$order = [
			'CashLogID' => $cash['LogID'],
			'RepayID' => $config['ID'],
			'Number' => $cash['Number'],
			'OrderID' => "No" . date('YmdHis') . get_uniqid(8),
			'AddTime' => time(),
			'AdminID' => $adminID,
			'Status' =>0,
			'IP' => $IP,
			'BankCard' => $cash['BankCard'],
			'BankName' => $cash['BankName'],
			'BankUser' => $cash['BankUser'],
			'BankAddress' => $cash['BankAddress'],
		];
		$order['ID'] = $this->app->RepayLog->insert($order);
		$NotifyUrl =  _CONF('staticFileUrl') . '/cash/index/notify/' . $config['ID'] . '_' . $order['ID'];
		$payment = ucfirst(strtolower($config['Channel']));
		if (is_file(__APPDIR__ . 'Controller/Cash/Interfaces/' . $payment . '.php')) {
			include_once _include(__APPDIR__ . 'Controller/Cash/Interfaces/' . $payment . '.php');
			$contor = '\\Cash\\Interfaces\\' . $payment;
			$pay = new $contor($this->app);
			return $pay->pay($order,$config,$NotifyUrl);
		} else {
			$this->app->response('0001', [], '支付平台等待接入');
		}
		return $order;
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
}