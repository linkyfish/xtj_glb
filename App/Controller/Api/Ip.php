<?php

namespace Api;

use Ctrl\Controller;

Class Ip extends Controller
{
	public function getIp()
	{
		$ip = $this->request->get_client_ip();

		$inputIp = $this->request->param('ip', '');
		if (!empty($inputIp)) {
			$ip = $inputIp;
		}
		$apiUrl = _CONF('aliyungetIpAreaDetailUrl') . '/ip?ip=';
		$AppCode = _CONF('aliyungetIpAreaDetailAppCode');
		$headers = ['Authorization' => 'APPCODE ' . $AppCode];
		$response = http_get_api($apiUrl . $ip, $headers);
		xn_log($ip . '-' . json_encode($this->request->server), 'getip');
		//xn_log($ip, 'player_ip');
		//xn_log(json_encode($this->request->cookie),'getip');
		//xn_log($response,'getiparea');
		$obj = json_decode($response);
		if ($obj->ret == 200) {
			$region = $obj->data->region;
			$ipArea = json_encode($obj->data);
			$this->Logintable->update(['ip' => $ip], ['ipregion' => $region, 'ipArea' => $ipArea]);
		}
		return trim($ip);
	}

	public function updateIpArea()
	{
		$where['ipregion'] = '0';
		$ipList = $this->Logintable->select([], [], 'IP,ipregion,count(id) as playerCount', 1, 1, '', 'group by IP');
		$num = count($ipList);
		$output = '';
		for ($i = 0; $i < $num; $i++) {
			$item = $ipList[$i];
			if (!empty($item['IP'])) {
				$ip = $item['IP'];
				$AppCode = _CONF('aliyungetIpAreaDetailAppCode');
				list($https, , $host) = explode('/', _CONF('aliyungetIpAreaDetailUrl'));
				list($host, $port) = explode(':', $host);
				$https = $https == 'https:' ? true : false;
				if (!$port) {
					$port = $https ? 443 : 80;
				}
				$cli = new \Swoole\Coroutine\Http\Client($host, $port, true);
				$header = [
					'Host' => $host,
					'Accept' => 'text/html,application/xhtml+xml,application/xml',
					'Accept-Encoding' => 'gzip',
					'Authorization' => 'APPCODE ' . $AppCode,
				];
				$cli->setHeaders($header);
				$cli->set(['timeout' => 5]);
				$cli->get('/ip?ip=' . $ip);
				$obj = json_decode($cli->body);
				$cli->close();
				if ($obj->ret == 200) {
					$region = $obj->data->region;
					$ipArea = json_encode($obj->data);
					$this->Logintable->update(['IP' => $ip], ['ipregion' => $region, 'ipArea' => $ipArea]);
					$output .= '更新IP详情:' . $ip . ' ' . date('Y-m-d H:i:s') . '成功';
					xn_log('更新IP详情:' . $ip . ' ' . date('Y-m-d H:i:s') . '成功', 'upIpTxt');
				} else {
					if ($obj->ret == 40003) {
						$this->Logintable->update(['IP' => $ip], ['ipregion' => '内网', 'ipArea' => '']);
						xn_log('更新IP详情:' . $ip . ' ' . date('Y-m-d H:i:s') . '失败:内网地址', 'upIpTxt');
					} else {
						$this->Logintable->update(['IP' => $ip], ['ipregion' => '8', 'ipArea' => '']);
						$output .= '更新IP详情:' . $ip . ' ' . date('Y-m-d H:i:s') . '失败';
						xn_log('更新IP详情:' . $ip . ' ' . date('Y-m-d H:i:s') . '失败', 'upIpTxt');
					}

				}
			}
		}

		return $output;

	}

	public function getIpInfo()
	{
		$ip = $this->request->get_client_ip();
		$inputIp = $this->request->param('ip', '');
		if (!empty($inputIp)) {
			$ip = $inputIp;
		}
		$apiUrl = _CONF('aliyungetIpAreaDetailUrl') . '/ip?ip=';
		$AppCode = _CONF('aliyungetIpAreaDetailAppCode');
		$headers = ['Authorization' => 'APPCODE ' . $AppCode];
		$response = http_get_api($apiUrl . $ip, $headers);
		return $response;
	}

	public function getRealIp()
	{
		return json_encode($this->request->_S('X-FORWARDED-FOR'));
	}

	public function getFangfengUrl()
	{

	}
}