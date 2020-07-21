<?php

namespace Ctrl;

// hook controller_use.php

/**
 * Class Controller
 *
 * @package Ctrl
 * @property \Request              $request
 * @property \Session              $session
 * @property \swoole_http_response $_response
 */
Class Controller extends \Server\Libs\Controller
{
	// hook controller_public_start.php

	public $token = [];
	public $request = [];
	public $session = [];
	public $_response = [];
	public $route = [];
	public $assign = [];
	public $is_ajax = '';
	public $_method = '';

	// hook controller_public_end.php
	public $agent_role_arr = [9, 10, 11];
	public $admin_role_arr = [1, 2, 3, 101, 102, 103, 104, 105, 106, 107, 108];

	public $bl = 1000;

	public function __construct($server, $route, \Request $request, $response)
	{
		parent::__construct($server, $route, $request, $response);
		$PlatformSetting = $this->PlatformSetting->CacheGet('PlatformSetting');
		if (empty($PlatformSetting)) {
			$PlatformSetting = $this->PlatformSetting->select([], [], 'name,val');
			$this->PlatformSetting->CacheSet('PlatformSetting', $PlatformSetting, 600);
		}
		foreach ($PlatformSetting as $v) {
			$_ENV['conf'][$v['name']] = $v['val'];
		}
	}

	// hook controller_start.php

	public function DestroyToken($name = 'token_uid')
	{

		// hook controller_destroytoken_start.php

		$this->_response->cookie(_CONF('cookie_tablepre') . _CONF('session_id'), '', time() - $this->session->life, '/');
		$this->_response->cookie($this->session->sid, '', time() - $this->session->life, '/');
		unset($this->session->sess[$name]);

		// hook controller_destroytoken_end.php
	}

	public function Authenticate($name = 'token_uid')
	{
		$user = !empty($this->session->sess[$name]) ? $this->User->read_by_id($this->session->sess[$name]) : [];
		// hook controller_authenticate_start.php
		if ($this->session->sess['user']['password'] != $user['password']) {
			$this->DestroyToken($name);
			$user = [];
		}
		// hook controller_userinfo_end.php
		return $this->token = $user;
	}

	public function UserInfo($name = 'token_uid', $url = '')
	{

		$sid = $this->request->param($this->session->sid);
		$_sid = xn_decrypt($sid);
		$uid = explode('-', $_sid)[1];
		$user = !empty($uid) ? $this->User->read_by_id($uid) : [];

		if ($this->session->sess['user']['password'] != $user['password'] || !empty($user['is_deleted']) || empty($user['id'])) {
			$this->DestroyToken($name);
			$this->response('0401', [], '', $url, 302);
		}
		if ($user['status'] != 1) {
			$this->DestroyToken($name);
			if($this->is_ajax){
				$this->response('0001', [], '账号已被锁定,请联系客服解锁');
			}else{
				$this->response('0001', [], '账号已被锁定,请联系客服解锁', $url, 302);
			}
		}

		// hook controller_userinfo_start.php
		$this->_response->cookie($this->session->sid, $sid, time() + $this->session->life, '/');
		$this->_response->cookie('manager_uid', $uid, time() + $this->session->life, '/');

//        $route = implode('_', $this->route);
//        $k = strtolower($route . '_' . $this->_method);

//        if (empty($_ENV['auth'][$k])) {
//            $k = strtolower($route);
//            $auth = $_ENV['auth'][$k];
//        } else {
//            $auth = $_ENV['auth'][$k];
//        }
//
//        if (empty($user['id']) && !empty($auth['is_login'])) {
//            if ($this->is_ajax) {
//                $this->response('0401');
//            } else {
//                $this->response('0401', [], '', $url, 302);
//            }
//        }
//        if (!empty($auth['is_auth'])) {
//            $au = $this->RoleAuth->read(['role_id' => $user['gid'], 'node' => $k]);
//            if (empty($au['role_id'])) {
//                if ($this->is_ajax) {
//                    $this->response('0402', [], $k);
//                } else {
//                    $this->response('0402', [], $k, $url, 302);
//                }
//            }
//        }
		$this->session->life = 3600;
		// hook controller_userinfo_end.php
		return $this->token = $user;
	}


	/**
	 * @param string $tpl
	 * @param array  $_data
	 *
	 * @return string
	 * @throws \Exception
	 * 加载模板文件书出PHP执行结果
	 */
	public function View($_data = [], $_tpl = '')
	{
		// hook controller_view_start.php
		$route = $this->route;
		unset($route[0]);
		empty($_tpl) AND $_tpl = implode('.', $route);
		$_tpl = (!empty($this->route[0]) ? $this->route[0] : 'Index') . '/View/' . $_tpl;

		$filename = !empty($_ENV['plugin_view_files'][$_tpl . '.html']) ? $_ENV['plugin_view_files'][$_tpl . '.html'] : __APPDIR__ . 'Controller/' . $_tpl . '.html';

		// hook controller_view_filename.php

		!is_file($filename) AND $this->response('0007', '', '模板不存在:' . $_tpl);
		$__PRE__ = implode('_', $route);
		if (is_array($_data)) {
			extract($_data, EXTR_OVERWRITE);
		}
		if ($this->assign) {
			extract($this->assign, EXTR_OVERWRITE);
		}
		ob_start();
		include _include($filename);
		$data = ob_get_contents();
		ob_end_clean();
		// hook controller_view_end.php
		return $data;
	}

	/**
	 * @param string $tpl
	 *
	 * @throws \Exception
	 * 通过接口请求返回模板源文件
	 */
	public function Template($_tpl = '', $_data = [])
	{
		// hook controller_template_start.php
		$route = $r = $this->route;
		unset($route[0]);
		empty($_tpl) AND $_tpl = implode('.', $route);
		$_tpl = (isset($this->route[0]) ? $this->route[0] : 'Index') . '/View/' . $_tpl;
		$filename = !empty($_ENV['plugin_view_files'][$_tpl . '.html']) ? $_ENV['plugin_view_files'][$_tpl . '.html'] : __APPDIR__ . 'Controller/' . $_tpl . '.html';
		// hook controller_template_filename.php
		!is_file($filename) AND $this->response('0007', '', '模板不存在:' . $_tpl);
		$__PRE__ = implode('_', $route);
		if (is_array($_data)) {
			extract($_data, EXTR_OVERWRITE);
		}
		if ($this->assign) {
			extract($this->assign, EXTR_OVERWRITE);
		}
		ob_start();
		include _include($filename);
		$data = ob_get_contents();
		ob_end_clean();
		// hook controller_template_end.php
		$this->response('0000', ['data' => ['tpl' => $data]]);
	}

	/**
	 * @param $value
	 * @param $text
	 *
	 * @throws \Exception
	 * 检测变量是否为空
	 */
	public function CheckEmpty($value, $text)
	{
		// hook controller_checkempty_start.php
		foreach ($value as $k => $val) {
			empty($val) AND $this->response('0003', [], $text[$k] . '不能为空');
		}
		// hook controller_checkempty_end.php
	}

	public function session_start()
	{
		$this->session = new \Session($this->User->cache, $this->request, $this->_response);
		$this->session->life = 7200;
	}


	// hook controller_end.php
}

?>