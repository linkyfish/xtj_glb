<?php

namespace Ashx;

use Ctrl\Controller;


Class Login extends Controller
{

    public function Index()
    {
        $this->response('0000');
    }
    public function Login_checkState()
    {
        $this->session_start();
        $this->UserInfo('token_uid', '../../');
        $this->response('0000');
    }

    public function Login_login_POST()
    {
		$this->session_start();
        // return 666;
        $client_user = $this->request->param('userName');
        $client_pwd = $this->request->param('passWd');
        $xcode = trim($this->request->param('xcode'));
        $safe_code = trim($this->request->param('safe_code',''));

        //代理不限制IP登录
        $isagent =   $this->request->_S("AGENT");
        if (empty($xcode)) {
            $msg = array('success' => false, 'msg' => '验证码不能为空.');
            $this->response('0001', $msg);
        }

		if(strtolower($xcode) != strtolower($this->session->sess['xcode'])){
			$msg = array('success' => false, 'msg' => '验证码错误.');
			$this->response('0001', $msg);
		}
		$user = $this->User->read_by_username($client_user);
		if (empty($user['id'])) {
			$msg = array('success' => false, 'msg' => '账号不存在.');
			$this->response('0001', $msg);
		}
        if (!empty($user['islock'])) {
            $msg = array('success' => false, 'msg' => '账号被锁定.');
            $this->response('0001', $msg);
        }

        /*
        if (empty($user['status'])) {
            $msg = array('success' => false, 'msg' => '账号被禁用.');
            $this->response('0001', $msg);
        }
        */

        xn_log(date('Y-m-d H:i:s'). '用户登录ip'.$this->request->get_client_ip(1).' - '.$this->request->get_client_ip(0),'userloginip');
        if (__LV__ != "dev") { //dev下不做限制
            if ($isagent) {
                !in_array($user['RoleID'], [9, 10, 11]) AND $this->response('0001', [], '账户飞走了');
            } else {
                //管理员需判断是否再可允许登录的IP范围里面
                in_array($user['RoleID'], [9, 10, 11]) AND $this->response('0001', [], '账户飞走了');
                $ipList = $this->AdminIpWhiteList->select(['adminId'=>$user['id']]);
				$ipList = arrlist_values($ipList,'ip');
                $ip = $this->request->get_client_ip();
				if(!empty($ipList) && !in_array($ip,$ipList)){
					$msg = array('success' => false, 'msg' => $ip . ' 限制登陆');
					$this->response('0001', $msg);
				}
            }
        }

		if(!empty($user['safe_code']))
		{
			if ($safe_code != $user['safe_code']) {
				$msg = array('success' => false, 'msg' => '安全码错误');
				$this->response('0001', $msg);
			}
		}


        if (md5($client_pwd . _CONF('salt')) != $user['password']) {
            $date = date('Y-m-d', $user['last_login_time']);
            if ($date == date('Y-m-d', time())) {
                $param = [
                    'login_failed_count+' => 1
                ];
                $this->User->update(['id' => $user['id']], $param);
                if ($user['login_failed_count'] + 1 >= 3)  //超过次数锁定账号
                {
                    $param['status'] = 0;
                    $param['islock'] = 1;
                    $msg = array('success' => false, 'msg' => '登录失败次数已经超过限制，账号被锁定.');
                    $this->OperateLog->Add($user['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '账户锁定');
                    $this->UserLockLog->Add($user['id'], $user['id'], $this->request->get_client_ip(1), 1);
                    $this->response('0001', $msg);
                }
            } else {
                $param = [
                    'login_failed_count' => 1
                ];
                $this->User->update(['id' => $user['id']], $param);
            }

            $msg = array('success' => false, 'msg' => '密码错误.');
            $this->OperateLog->Add($user['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '密码错误 '.$user['login_failed_count']);
            $this->response('0001', $msg);
        }


        $this->User->update(['id' => $user['id']], ['last_login_time' => time(),'login_failed_count' => 0,'last_login_ip'=> $this->request->get_client_ip()]);
        $this->session_start();
        $this->session->sess['user'] = $user;
        $this->session->sess['token_uid'] = $user['id'];
        $this->_response->cookie($this->session->sid, xn_encrypt($this->session->sid.'-'.$user['id']), time() + $this->session->life, '/');
        $this->OperateLog->Add($user['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '账户登陆成功');
        $msg = array('success' => true, 'msg' => '登录成功.', 'href' => "/com/playerList.aspx?t=". time());
        if(in_array($user['RoleID'],[9,10,11]))
        {
            $msg = array('success' => true, 'msg' => '登录成功.', 'href' => "/com/userList.aspx?rid=" . $user['username'] . '&t=' . time());
        }

        if(empty($user['safe_code']))
        {
            $msg = array('success' => true, 'msg' => '登录成功.', 'href' => "/com/safecode.aspx?rid=" . $user['username'] . '&t=' . time());
        }

        $this->response('0000', $msg);

    }
}
