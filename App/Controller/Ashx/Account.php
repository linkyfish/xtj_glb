<?php
/**
 * Created by PhpStorm.
 * User: yinhanmin
 * Date: 2019-03-09
 * Time: 08:47
 */

namespace Ashx;


use Co;
use Ctrl\GameController;

class Account extends GameController
{

    public function Account_GetAccount()
    {
        $ssid = $this->request->param('ssid');
        $this->response('0000', ['onDisable' => $ssid, 'account' => $ssid]);
    }

    public function Account_MoveAccount()
    {
        $this->needadmin();

        $userName = $this->request->param('userName', '');
        $agentName = $this->request->param('agentName', '');
        $types = $this->request->param("types", "");
        //$can_move = $this->UserSettlement->read(['Ymd'=>date("Ymd",strtotime('-1 day'))]);
        //empty($can_move['Ymd']) && $this->response('0001', [], '结算日结算以后当日可以转移账户,其他时间禁止转移');
        (empty($userName) || empty($agentName)) AND $this->response('0001', [], '请输入账号');

        $parent_agent = $this->User->read_by_username($agentName);
        if (empty($parent_agent['id'])) {
            $parent_agent = $this->User->read_by_id($agentName);
        }
        if (empty($parent_agent['id'])) {
            $this->response('0001', [], '代理不存在');
        }

        //转移代理
        if ($types == 'agent') {
            if ($parent_agent["RoleID"] == 9) {
                $this->response('0001', [], '代理账户不能移动到扩展号下');
            }
            $agent = $this->User->read_by_username($userName);
            if (empty($agent['id'])) {
                $agent = $this->User->read_by_id($userName);
            }
            if (empty($agent['id'])) {
                $this->response('0001', [], '代理不存在');
            }

            //判断占成比例
            if ($agent["share_rate"] >= $parent_agent["share_rate"]-1) {
                $this->response('0001', [], '被转移代理号占成比例高于目标代理，不能转移');
            }

            if ($agent['RoleID'] == 9 && $parent_agent["RoleID"] != 10) {
                $this->response('0001', [], '扩展号需移动到主号下');
            }
            if ($agent['RoleID'] == 10 && $parent_agent["RoleID"] != 11) {
                $this->response('0001', [], '主号需移动到管理号下');
            }

            $this->User->update(['id' => $agent['id']], ['parent_id' => $parent_agent['id']]);
            //不能互为上下级 如果当前要操作的 上级转移到直属下级 那么当前下级需要继承到当前上级的代理
            if ($parent_agent["parent_id"] == $agent["id"]) {
                $this->User->update(['id' => $parent_agent['id']], ['parent_id' => $agent['parent_id']]);// $this->response('0001', [], '修改失败');
            }

            $oagent = $this->User->read_by_id($agent['parent_id']);
            $insert = [
                'UID' => $agent['id'],
                'Account' => $this->token['username'],
                'UserName' => $agent['username'],
                'Type' => 20,
                'BeforeAgentID' => $agent['parent_id'],
                'BeforeAgent' => $oagent['username'] ? $oagent['username'] : "",
                'AfterAgentID' => $parent_agent['id'],
                'AfterAgent' => $parent_agent['username'],
            ];
            $this->UserMove->insert($insert);
            $this->OperateLog->Add($this->token['id'], $agent['id'], 1, $this->request->get_client_ip(1), "移动 " . $agent['username'] . '(' . $agent['id'] . ") -> " . $parent_agent['username']);
        } else {
            //转移玩家
            $user = $this->Usr->read(["openid" => $userName]);
            if (empty($user)) {
                //查询id
                $user = $this->Usr->read(["usrid" => $userName]);
            }
            if (empty($user)) {
                $this->response('0001', [], '查询不到该玩家的信息，请确认账号是否正确');
            }
            if ($parent_agent['RoleID'] != 9) {
                $this->response('0001', [], '玩家只能移动到扩展号下');
            }

            $this->Usr->update(['usrid' => $user['usrid']], ['agent_id' => $parent_agent['id'], 'merchant_code' => $parent_agent['code']]);// $this->response('0001', [], '修改失败');
            $this->Account->update(['account' => $user['openid']], ['merchant_code' => $parent_agent['code']]);// $this->response('0001', [], '修改失败');
            $oagent = $this->User->read_by_id($user['agent_id']);
            $insert = [
                'UID' => $user['usrid'],
                'Account' => $this->token['username'],
                'UserName' => $user['openid'],
                'Type' => 10,
                'BeforeAgentID' => $oagent['id'] ? $oagent['id'] : 0,
                'BeforeAgent' => $oagent['username'] ? $oagent['username'] : "",
                'AfterAgentID' => $parent_agent['id'],
                'AfterAgent' => $parent_agent['username'],
            ];

            $this->UserMove->insert($insert);
            $this->OperateLog->Add($this->token['id'], $user['usrid'], 0, $this->request->get_client_ip(1), "移动 " . $oagent['username'] . " -> " . $parent_agent['username']);
        }
        $this->response('0000');
    }

    public function Account_OnSettingRegSong()
    {
        $this->token['RoleID'] == 11 && $this->response('0001', [], '管理号无权操作');
        $user = $this->request->param('user', '');
        $setSongFlag = $this->request->param('setSongFlag', 0);
        $songTimes = $this->request->param('songTimes', 0);
        $user = $this->User->read_by_username($user);
        $this->hspower($user['id']);
        if (empty($songTimes)) {
            $this->User->update(['id' => $user['id']], ['is_reg_give' => $setSongFlag]);
            $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '设置注册送 ' . ($setSongFlag ? '开启' : '关闭'));
            $this->response('0000');
        }

        $Parent = $this->User->read_by_id($user['parent_id']);
        if ($Parent['is_reg_give'] != 1) {
            //$this->response('0001', [], '上级没有开启注册送哦');
        }
        $this->hspower($Parent['id']);

        if ($songTimes > 0) {
            if ($songTimes > $Parent['reg_set']) {
                $this->response('0001', [], '超过上级代理注册送最大数量');
            }
        } else if (-$songTimes > $user['reg_set']) {
            $this->response('0001', [], '超过自身注册送最大数量');
        }
        $this->User->update(['id' => $user['parent_id']], ['reg_give_num+' => $songTimes, 'reg_set-' => $songTimes]);
        $this->User->update(['id' => $user['id']], ['reg_set+' => $songTimes, 'is_reg_give' => $setSongFlag]);
        $user['reg_set'] += $songTimes;
        $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '设置注册送数量[' . $songTimes . ']修改后数量[' . $user['reg_set'] . ']');
        $this->response('0000');
    }

    public function Account_OnSettingSmsSong()
    {
        $this->response('0001',[],'短信送关闭');
        $this->token['RoleID'] == 11 && $this->response('0001', [], '管理号无权操作');
        $user = $this->request->param('user', '');
        $setSongFlag = $this->request->param('setSongFlag', 0);
        $songTimes = $this->request->param('songTimes', 0);
        $user = $this->User->read_by_username($user);
        $this->hspower($user['id']);
        if (empty($songTimes)) {
            $this->User->update(['id' => $user['id']], ['is_sms_give' => $setSongFlag]);
            $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '设置短信送 ' . ($setSongFlag ? '开启' : '关闭'));
            $this->response('0000');
        }

        $Parent = $this->User->read_by_id($user['parent_id']);
        if ($Parent['is_sms_give'] != 1) {
            //$this->response('0001', [], '上级没有开启短信送哦');
        }
        $this->hspower($Parent['id']);

        if ($songTimes > 0) {
            if ($songTimes > $Parent['sms_set']) {
                $this->response('0001', [], '超过上级代理短信送最大数量');
            }
        } else if (-$songTimes > $user['sms_set']) {
            $this->response('0001', [], '超过自身短信送最大数量');
        }
        $this->User->update(['id' => $user['parent_id']], ['sms_give_num+' => $songTimes, 'sms_set-' => $songTimes]);
        $this->User->update(['id' => $user['id']], ['sms_set+' => $songTimes, 'is_sms_give' => $setSongFlag]);
        $user['sms_set'] += $songTimes;
        $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '设置短信送数量[' . $songTimes . ']修改后数量[' . $user['sms_set'] . ']');
        $this->response('0000');
    }

    public function Account_doUpdateSetting()
    {
        $user = $this->request->param('user', '');
        $is_allow_addagent = $this->request->param('is_allow_addagent', 0);
        $is_allow_addPromotion = $this->request->param('is_allow_addPromotion', 0);
        $setPWD = $this->request->param('setPWD', 0);
        $Apply = $this->request->param('Apply', 0);
        $User = $this->User->read_by_username($user);
        $this->hspower($User['id']);
        $arr = ['is_allow_addagent' => $is_allow_addagent, 'is_allow_addPromotion' => $is_allow_addPromotion, 'is_req_give' => $Apply, 'is_allow_ad_pwd' => $setPWD];
        $r = $this->User->update(['id' => $User['id']], $arr);
        $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '设置权限 新增代理[' . ($arr['is_allow_addagent'] == 1 ? '开' : '关') . '] 申请送[' . ($arr['is_req_give'] == 1 ? '开' : '关') . '] 改扩展密码[' . ($arr['is_allow_ad_pwd'] == 1 ? '开' : '关') . ']');
        $this->response($r ? '0000' : '0001');
    }


    public function Account_EnableAll()
    {
        $agent = $this->request->param('agent', '');
        $type = $this->request->param('type', 0);
        $User = $this->User->read_by_username($agent);
        $this->hspower($User['id']);
        $User['RoleID'] != 9 AND $this->response('0001', [], '本操作只能针对扩展号');
        !$this->isadmin AND $this->response('0001', [], '操作权限不足');
        $cond = ['agent_id' => $User['id']];
        $num = $this->Usr->select($cond, [], 'usrid');
        $updata = ['disable' => $type == 2 ? 1 : 0];
        $type == 2 && $updata['online'] = 0;
        $this->Usr->update($cond, $updata);
        $this->OperateLog->Add($this->token['id'], $User['id'], 1, $this->request->get_client_ip(1), '批量' . ($type == 2 ? '禁用' : '启用') . '[' . $User['username'] . ']下所有玩家[' . count($num) . ']个');

        if ($type == 2) {
            $usrid = arrlist_values($num, 'usrid');
            $update_url = 'http://' . _CONF('gameServerIp') . ':9090/' . _CONF('kickPlayerApiUrl');
            foreach ($usrid as $id) {
                $time = time();
                $formData = [
                    'usrid' => $id,
                    'times' => $time,
                    'token' => getApiToken($time),
                ];
                post_api($update_url, $formData);
            }
        }

        $this->response('0000');
    }


    public function Account_LogOut()
    {
        $this->DestroyToken();
        $this->response('0000', [], '安全退出');
    }

    public function Account_Get_weChatID()
    {
        $data['BankCardID'] = $this->default_user['bankNo'];
        $data['BankAddress'] = $this->default_user['bankName'];
        $data['Name'] = $this->default_user['realname'];
        $data['Tel'] = $this->default_user['phone'];
        $data['weChatID'] = $this->default_user['wechat'];
        $this->response('0000', $data);
    }

    public function Account_Edit_weChatID()
    {

        /*
        $data['bankNo'] = $this->request->param('BankCardID', '');
        $data['bankName'] = $this->request->param('BankAddress', '');
        $data['realname'] = $this->request->param('Name', '');
        $data['phone'] = $this->request->param('Tel', '');
        */
        $data['wechat'] = $this->request->param('weChatID', '');
        $data['QQ'] = $this->request->param('QQ', '');
        if ($this->default_user['bankNo']) {
            unset($data['bankNo'], $data['bankName']);
        }
        $this->User->update(['id' => $this->token['id']], $data);
        $this->response('0000');
    }

    public function Account_setPlatUserPwd()
    {
        empty($this->roleInfo['gm']) AND $this->response('0001', [], '无权修改密码');
        $_user = $this->request->param('userName', '');
        $Newpwd = $this->request->param('Newpwd', '');
        $user = trim(str_replace('☆', '', $_user));
        if ($user != $_user) {//玩家
            if ($this->isadmin) {
                if (isset($user{10})) {
                    $player = $this->Account->read_one(['account' => $user]);
                } else {
                    $player = $this->Account->read_one(['usrid' => $user]);
                }
            } else {
                $player = $this->Account->read_one(['usrid' => $user]);
            }
            $this->hspower($player['usrid'], 1);

            $tempRand = mt_rand(100000000, 99999999);
            $param['times'] = date("Y-m-d H:i:s");
            $param['password'] = md5($tempRand . $Newpwd . date("Y-m-d"));
            $param['checktoken'] = $tempRand . '/' . $param['times'];
            $r = $this->Account->update(['account' => $player['account']], $param);
            $this->OperateLog->Add($this->token['id'], $player['usrid'], 0, $this->request->get_client_ip(1), '修改密码');

        } else {
            $user = $this->User->read_by_username($user);
            $this->hspower($user['id']);
            $pasword = md5($Newpwd . _CONF('salt'));
            if ($pasword == $user['password']) {
                $this->response('0001', '', '新旧密码一样,不修改');
            }

            $r = $this->User->update(['id' => $user['id']], ['password' => $pasword]);
            $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '修改密码');
        }
        $this->response($r ? '0000' : '0001');
    }

    public function Account_setPlatAgentSafecode()
    {
        empty($this->roleInfo['gm']) AND $this->response('0001', [], '无权修改安全码');
        $_user = $this->request->param('userName', '');
        $Newpwd = $this->request->param('Newpwd', '');
        $user = trim(str_replace('☆', '', $_user));
        $user = $this->User->read_by_username($user);
        $this->hspower($user['id']);
        if (strlen($Newpwd) != 4) {
            $this->response('0001', '', '安全码长度必须是4位');
        }

        if ($Newpwd == $user['safe_code']) {
            $this->response('0001', '', '新旧安全码一样,不修改');
        }
        $r = $this->User->update(['id' => $user['id']], ['safe_code' => $Newpwd]);
        $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '修改安全码');

        $this->response($r ? '0000' : '0001');
    }


    public function Account_disable()
    {
        $this->needadmin();
        $_user = $this->request->param('user', '');
        $user = trim(str_replace('-', '', $_user));

        if ($user != $_user) {//玩家
            if ($this->isadmin) {
                if (isset($user{10})) {
                    $player = $this->Usr->read(['openid' => $user]);
                } else {
                    $player = $this->Usr->read(['usrid' => $user]);
                }
            } else {
                $player = $this->Usr->read(['usrid' => $user]);
            }
            $this->hspower($player['usrid'], 1);
            $r = $this->Usr->update(['usrid' => $player['usrid']], ['disable' => $player['disable'] == 1 ? 0 : 1]);
            $rAccout = $this->Account->update(['account' => $user], ['disable' => $player['disable'] == 1 ? 0 : 1, 'errorNumber' => 0]);

            $desc = ($player['disable'] == 1 ? '启用' : '禁用') . ' 玩家账户 ' . $player['openid'] . '(' . $player['usrid'] . ')';
            $this->OperateLog->Add($this->token['id'], $player['usrid'], 0, $this->request->get_client_ip(1), $desc);

            if ($player['disable'] == 0) {

                $update_url = 'http://' . _CONF('gameServerIp') . ':9090/' . _CONF('kickPlayerApiUrl');
                $time = time();
                $formData = [
                    'usrid' => $player['usrid'],
                    'times' => $time,
                    'token' => getApiToken($time),
                ];
                post_api($update_url, $formData);
            }

        } else {
            $user = $this->User->read_by_username($user);
            $this->hspower($user['id']);
            $r = $this->User->update(['id' => $user['id']], ['status' => $user['status'] == 1 ? 0 : 1]);
            $desc = ($user['status'] == 1 ? '禁用' : '启用') . ' 账户 ' . $user['username'] . '(' . $user['id'] . ')';
            $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), $desc);
        }

        $this->response($r ? '0000' : '0001');
    }


    public function account_getSearchUserInfo()
    {
        $userName = $this->request->param('userName', '');
        $type = $this->request->param('type', 1);
        $data['InfoID'] = $userName;
        $data['results'] = [];

        if ($type == 1) {
            $data['UserTYPE'] = 'player';
            $user_info = $this->findPlayer($userName);
            $agent = $this->User->read_by_id($user_info['agent_id']);
            $agent_id = $agent['id'];
            $data['results'][] = $this->Account->format($user_info, time(), $this->roleInfo, $this->agent_id_name);
            empty($user_info) AND $this->response('0001', [], '无此帐号或无权操作此帐号.');
        } else {
            if (strlen($userName) == 6 || !is_numeric($userName)) {
                $user_info = $this->User->read_by_username($userName);
            } else {
                $user_info = $this->User->read_by_id($userName);
            }

            empty($user_info['id']) AND $this->response('0001', [], '账户不存在.');
            $this->hspower($user_info['id']);
            $data['UserTYPE'] = 'agent';
            if ($user_info['id']) {
                $agent_id = $user_info['id'];
                $data['results'][] = $this->User->format($user_info, $this->roleInfo);
            }
        }
        $data['upListArr'] = [];
        for ($i = 0; $i < 50; $i++) {
            if (empty($agent_id)) {
                break;
            }
            if (!$this->isadmin && $user_info['id'] == $this->default_user['id']) {
                $agent_id = 0;
                break;
            }
            $user_info = $this->User->read_by_id($agent_id);
            $agent_id = $user_info['parent_id'];
            if ($user_info) {
                $vv = $this->User->format($user_info, $this->roleInfo);
                $vv['Rownum'] = $i + 1;
                $data['upListArr'][] = $vv;
            }


        }

        $this->response('0000', $data);
    }

    public function account_getSearchAgentUserInfo()
    {
        $userName = $this->request->param('userName', '');
        $type = $this->request->param('type', 1);
        $data['InfoID'] = $userName;
        $data['results'] = [];

        if ($type == 1) {
            $data['UserTYPE'] = 'player';
            $user_info = $this->findPlayer($userName);
            $agent = $this->User->read_by_id($user_info['agent_id']);
            $agent_id = $agent['id'];
            $accountInfo = $this->Account->formatAgentUser($user_info, time(), $this->roleInfo, $this->agent_id_name);
            $accountInfo['Agent'] = $this->agent_id_guid[$agent_id];
            $accountInfo['isUserAgent'] = 0;

            if($agent['RoleID'] ==111)
            {
                $accountInfo['Agent'] = $agent['guid'];
                $accountInfo['isUserAgent'] = 1;

                $parentUserInfo = $this->Usr->read(['usrid'=>$agent['guid']]);
                if($parentUserInfo)
                {
                    $accountInfo['Agent'] = $parentUserInfo['openid'];
                }

            }
            $data['results'][] = $accountInfo;
            empty($user_info) AND $this->response('0001', [], '无此帐号或无权操作此帐号.');
        } else {
            if (strlen($userName) == 6 || !is_numeric($userName)) {
                $user_info = $this->User->read_by_username($userName);
            } else {
                $user_info = $this->User->read_by_id($userName);
            }

            empty($user_info['id']) AND $this->response('0001', [], '账户不存在.');
            $this->hspower($user_info['id']);
            $data['UserTYPE'] = 'agent';
            if ($user_info['id']) {
                $agent_id = $user_info['id'];
                $data['results'][] = $this->User->format($user_info, $this->roleInfo);
            }
        }
        $data['upListArr'] = [];
        /*
        for ($i = 0; $i < 1; $i++) {
            if (empty($agent_id)) {
                break;
            }
            if (!$this->isadmin && $user_info['id'] == $this->default_user['id']) {
                $agent_id = 0;
                break;
            }
            $user_info = $this->User->read_by_id($agent_id);
            $agent_id = $user_info['parent_id'];
            if ($user_info) {
                $vv = $this->User->format($user_info, $this->roleInfo);
                $vv['Rownum'] = $i + 1;
                if($user_info['RoleID'] ==111)
                {
                    $data['upListArr'][] = $vv;
                }
            }
        }*/

        $this->response('0000', $data);
    }


    public function account_getAccountInfo()
    {
        $userName = $this->request->param('userName', '');
        $user = $this->findPlayer($userName);
        $this->hspower($user['usrid'],1);
        $data['Type'] = 0;
        if ($this->roleInfo['dh']) {
            $data['username'] = $user['openid'];
        } else {
            $data['username'] = $user['usrid'];
        }

        $data['UserLimitNum'] = calculateSub2($user['bankchips'] + $user['chips']);
        $time = time();
        $list =	$this->Gamebilog->select(['UsrID'=>$user['usrid'],'Status'=>0]);
        $max = 86400*30;
        $Money=0;
        foreach ($list as $row){
            if($row['Type']==36|| $row['Type']==50){
                if($time-$row['AddTime']>$max){
                    $this->Gamebilog->update(['LogID'=>$row['LogID']],['Status'=>2]);
                }
                continue;
            }
            $Money+=$row['Money'];
        }

        $Money>0 && $data['UserLimitNum'] .='<i title="等待处理">('.calculateSub2($Money).')</i>';
        $this->response('0000', $data);
    }


    public function account_DelM()
    {

        $userName = $this->request->param('userName', '');
        $user = $this->User->read_by_username($userName);
        $this->hspower($user['id']);
        !$this->User->update(['id' => $user['id']], ['status' => $user['status'] == 1 ? 0 : 1]) AND $this->response('0001', [], '修改失败');
        $content = ($user['status'] == 1 ? "禁用" : "启用") . " 子帐号 {$user['username']}";
        $tmpIp = $this->request->get_client_ip(1);
        $this->OperateLog->Add($this->token['id'], $user['id'], 1, $tmpIp, $content);
        $this->response('0000');
    }

    public function account_checkUser()
    {

        $user = trim($this->request->param('userName', ''));
        empty($user) AND $this->response('0001', [], '请输入用户名');
        $this->User->read(['username' => $user]) AND $this->response('0001', [], '账户存在');
        $this->response('0000');
    }


    public function account_checkInviteCode()
    {
        $user = $this->request->param('userName', '');
        empty($user) AND $this->response('0001', [], '请输入推荐码');
        $this->User->read(['username' => $user]) AND $this->response('0001', [], '推广码存在');
        $this->response('0000');
    }


    public function account_addSubAccount()
    {
        $userName = trim($this->request->param('userName', ''));
        $PassWd = trim($this->request->param('PassWd', ''));
        $RoleID = $this->request->param('RoleID', 0);
        $Memo = trim($this->request->param('Memo', ''));
        $this->needadmin();
        $adminUser = $this->User->read_by_username($userName);
        if (!empty($adminUser['id'])) {
            $this->response('0001', [], '账户重复');
        }

        if ($RoleID < 100) {
            $this->response('0001', [], '权限不足');
        }

        $reg = [
            'register_time' => time(),
            'realname' => $userName,
            'nickname' => $userName,
            'level_char' => -2,
            'level_num' => -2,
        ];

        $reg['username'] = $userName;
        $reg['RoleID'] = $RoleID;
        $reg['password'] = md5($PassWd . _CONF('salt'));

        $reg['status'] = 0;
        $reg['Redme'] = $Memo;
        $id = $this->User->insert($reg);
        empty($id) AND $this->response('0001', [], '添加失败');

        $content = "新增了子帐号 id:{$id} 帐号:{$userName}";
        $this->OperateLog->Add($this->token['id'], $id, 1, $this->request->get_client_ip(1), $content);
        $this->response('0000');
    }

    /**
     * 添加代理
     * account_addUser
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_addUser()
    {
        $agentid = $this->request->param('agent', '');
        $userName = $this->request->param('userName', '');
        $PassWd = $this->request->param('PassWd', '');
        $Name = $this->request->param('Name', '');
        $Tel = $this->request->param('Tel', '');
        $Memo = $this->request->param('Memo', '');
        $UserType = $this->request->param('UserType', '');
        //$InviteCode = $this->request->param('InviteCode', '');
        $weChatID = $this->request->param('weChatID', '');
        $BankCardID = $this->request->param('BankCardID', '');
        $BankAddress = $this->request->param('BankAddress', '');
        $addAgentFlag = $this->request->param('addAgentFlag', 0);
        $ShareRate = $this->request->param('ShareRate', '');;

        $this->User->read(['username' => $userName]) AND $this->response('0001', ['type' => 2], '操作失败,已存在该帐号.');
        //$this->User->read(['username' => $InviteCode]) AND $this->response('0001', [], '操作失败,已存在该帐号.');
        isset($Memo{59}) AND $this->response('0001', [], '操作失败,备注太长了.');
        $agent = $this->User->read_by_username($agentid);
        if ($ShareRate > $agent['share_rate'] - 2 && $ShareRate!=0) {
            $this->response('0001', [], '占成比例不能大于上级.');
        }
        if ($ShareRate > 100 || $ShareRate < 0) {
            $this->response('0001', [], '占成比例应在0-100之间');
        }

        $data = [];
        $add = $this->User->MakeName($agent, $userName);
        $level = substr($add['level_char'],0,1);
        if($level=="G"){
            $this->response('0001',[],'注册失败');
        }
        $add['RoleID'] = 11;
        $add['Redme'] = $Memo;
        $add['parent_id'] = $agent['id'];
        $add['phone'] = $Tel;
        $add['wechat'] = $weChatID;
        $add['bankNo'] = $BankCardID;
        $add['bankSon'] = $BankAddress;
        $son['register_time'] = $add['register_time'] = time();
        $son['reg_mark'] = $add['reg_mark'] = $agent['reg_mark'];
        $son['nickname'] = $add['nickname'] = $Name;
        $son['is_allow_addagent'] = $add['is_allow_addagent'] = 1;
        $son['is_allow_addPromotion'] = $add['is_allow_addPromotion'] = 1;
        $son['is_allow_ad_pwd'] = $add['is_allow_ad_pwd'] = 0;
        $son['password'] = $add['password'] = md5($PassWd . _CONF('salt'));
        $son['status'] = $add['status'] = 1;
        $son['share_rate'] = $add['share_rate'] = max(0, $ShareRate);

        if ($add['level_num'] > 1) {
            $son['level_way'] = $add['level_way'];
            $son['username'] = ucfirst(strtolower(substr($add['username'], 0, 2))) . $add['level_way'];
            $son['RoleID'] = 10;
            $son['level_char'] = $add['level_char'];
            $son['level_num'] = 1;
        }

        $data['add'] = $add;

        if ($id = $this->User->insert($add)) {
            $son['parent_id'] = $id;
            if (!empty($son['level_num'])) {
                $son['id'] = $this->User->insert($son);
                $data['son'] = $son;
            }
            $intro = '新增管理号';
            $this->OperateLog->Add($this->token['id'], $id, 1, $this->request->get_client_ip(1), $intro);
            if ($son['id']) {
                $intro = '新增主号';
                $this->OperateLog->Add($this->token['id'], $son['id'], 1, $this->request->get_client_ip(1), $intro);
            }

            $this->User->CacheDel('Agent_Array');
            $this->User->CacheDel('Agent_Son');

        } else {
            $this->response('0001', ['type' => 1], '操作失败,请重试.');
        }
        $this->response('0000', $data);
    }

    /**
     * 添加扩展号
     * account_addUser
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_addPromoUser()
    {
        $agentid = $this->request->param('agent', '');
        $userName = $this->request->param('userName', '');
        $PassWd = $this->request->param('PassWd', '');
        $Name = $this->request->param('Name', '');
        $Tel = $this->request->param('Tel', '');
        $Memo = $this->request->param('Memo', '');
        $UserType = $this->request->param('UserType', '');
        //$InviteCode = $this->request->param('InviteCode', '');
        $weChatID = $this->request->param('weChatID', '');
        $BankCardID = $this->request->param('BankCardID', '');
        $BankAddress = $this->request->param('BankAddress', '');
        $addAgentFlag = $this->request->param('addAgentFlag', 0);
        $ShareRate = $this->request->param('ShareRate', '');;

        $this->User->read(['username' => $userName]) AND $this->response('0001', [], '操作失败,已存在该帐号.');
        //$this->User->read(['username' => $InviteCode]) AND $this->response('0001', [], '操作失败,已存在该帐号.');
        isset($Memo{59}) AND $this->response('0001', [], '操作失败,备注太长了.');
        $agent = $this->User->read_by_username($agentid);
        if ($ShareRate > $agent['share_rate']) {
            $this->response('0001', [], '占成比例不能大于上级.');
        }
        if ($ShareRate > 100 || $ShareRate < 0) {
            $this->response('0001', [], '占成比例应在0-100之间');
        }


        $data = [];
        $add = [];

        $add['RoleID'] = 9;
        $add['Redme'] = $Memo;
        $add['parent_id'] = $agent['id'];
        $add['phone'] = $Tel;
        $add['wechat'] = $weChatID;
        $add['bankNo'] = $BankCardID;
        $add['bankSon'] = $BankAddress;
        $add['register_time'] = time();
        $add['realname'] = $Name;
        $add['nickname'] = $Name;
        $add['is_allow_addagent'] = 0;
        $add['is_allow_addPromotion'] = 0;
        $add['is_allow_ad_pwd'] = 0;
        $add['password'] = md5($PassWd . _CONF('salt'));
        $add['status'] = 1;
        $add['share_rate'] = max(0, $ShareRate);
        $add['reg_mark'] = $agent['reg_mark'];

        $add['code'] = $add['username'] = $userName;
        $add['level_way'] = $agent['level_way'];
        $add['level_num'] = 1;
        $add['level_char'] = -1;

//        $level = strpos($level_char, $agent['level_char']);
//        $level = $level === false ? 0 : $level + 1;
//
//        $num = $this->User->count(['level_char' => $level_char{$level}, 'level_num' => $agent['level_num'] + 1, 'RoleID' => 11]);
//        $add['level_way'] = str_pad($agent['level_way'], max(2, ($level + 1) * 2), 0, STR_PAD_LEFT) . str_pad($num + 1, 2, 0, STR_PAD_LEFT);
//        $add['level_num'] = $agent['level_num'] + 1;
//        $add['level_char'] = $level_char{$level};
//

        $data['add'] = $add;

        if ($id = $this->User->insert($add)) {
            $intro = '新增扩展号';
            $this->OperateLog->Add($this->token['id'], $id, 1, $this->request->get_client_ip(1), $intro);
            $this->User->CacheDel('Agent_Array');
            $this->User->CacheDel('Agent_Son');

        } else {
            $this->response('0001', ['type' => 1], '操作失败,请重试.');
        }
        $this->response('0000', $data);
    }

    /**
     * 批量添加扩展号
     * account_addUser
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_addPromotion()
    {
        if ($this->token['RoleID'] != 1 && $this->token['RoleID'] != 2) {
            $this->response('0001', [], '无权操作');
        }
        $agentid = $this->request->param('agent', '');
        $value = $this->request->param('value', '');
        $this->CheckEmpty([$agentid, $value], ['上级代理', '待添加代理']);
        $agent = $this->User->read_by_username($agentid);
        $agent['RoleID'] != 10 AND $this->response('0001', [], '上级代理非主号，不能创建扩展号');
        $num = 0;
        $err = 0;
        $t = 0;
        foreach (explode(',', $value) as $user) {
            if (empty($user) || !isset($user{5})) {
                continue;
            }
            $t += 1;
            $u = $this->User->read_by_username($user);
            if ($u['RoleID']) {
                $err += 1;
                continue;
            }
            $add = [];
            $add['RoleID'] = 9;
            $add['parent_id'] = $agent['id'];
            $add['register_time'] = time();
            $add['nickname'] = $user;
            $add['is_allow_addagent'] = 0;
            $add['is_allow_addPromotion'] = 0;
            $add['is_allow_ad_pwd'] = 0;
            $add['password'] = md5(get_uniqid(6) . _CONF('salt'));
            $add['status'] = 1;
            $add['share_rate'] = $agent['share_rate'];
            $add['code'] = $add['username'] = $user;
            $add['level_way'] = $agent['level_way'];
            $add['level_num'] = 1;
            $add['level_char'] = -1;
            $id = $this->User->insert($add);
            $this->OperateLog->Add($this->token['id'], $id, 1, $this->request->get_client_ip(1), '批量新增扩展号');
            $num += 1;
        }

        if ($num) {
            $this->User->CacheDel('Agent_Array');
            $this->User->CacheDel('Agent_Son');
        } else {
            $this->response('0001', ['type' => 1], '没有创建任何账号.');
        }
        $this->response('0000', [], '总共【' . $t . '】，成功【' . $num . '】个，重复【' . $err . '】');
    }

    /**
     * 编辑用户
     * editUser
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_editUser()
    {
        $this->needadmin();
        $agentid = $this->request->param('agent', '');
        $userName = $this->request->param('userName', '');
        $PassWd = $this->request->param('PassWd', '');
        $Name = $this->request->param('Name', '');
        $Tel = $this->request->param('Tel', '');
        $Memo = $this->request->param('Memo', '');

        $s_playerID = $this->request->param('playerID', '');
        $weChatID = $this->request->param('weChatID', '');
        $BankCardID = $this->request->param('BankCardID', '');
        $BankAddress = $this->request->param('BankAddress', '');
        $addAgentFlag = $this->request->param('addAgentFlag', 0);
        $ShareRate = $this->request->param('ShareRate', '');;
        $add = [];
        $user = $this->User->read_by_username($userName);
        $this->hspower($user['id']);
        if (!empty($s_playerID)) {
            $player = $this->findPlayer($s_playerID);
            if ($game_user = $this->User->read(['game_user_id' => $player['usrid']])) {
                $game_user['id'] != $user['id'] && $this->response('0001', [], '该收益账号已绑定其他管理');
            }
            $add['game_user_id'] = $player['usrid'];
        }

        //$this->User->read(['username' => $InviteCode]) AND $this->response('0001', [], '操作失败,已存在该帐号.');
        isset($Memo{59}) AND $this->response('0001', [], '操作失败,备注太长了.');
        $agent = $this->User->read_by_username($agentid);
        if ($ShareRate > $agent['share_rate']) {
            $this->response('0001', [], '占成比例不能大于上级.');
        }
        if ($ShareRate > 100 || $ShareRate < 0) {
            $this->response('0001', [], '占成比例应在0-100之间');
        }

        if ($agent['parent_id']) {
            $Parent_agent = $this->User->read_by_id($agent['parent_id']);
            if ($Parent_agent['share_rate'] && $Parent_agent['share_rate'] < $ShareRate) {
                $this->response('0001', [], '比例不能高于上级代理');
            }
        }

        $son_agent = $this->User->find_one(['parent_id' => $user['id']], ['share_rate' => -1]);
        if (!empty($son_agent['share_rate']) && $son_agent['share_rate'] > $ShareRate) {
            $this->response('0001', [], '比例不能低于下级代理 ' . $son_agent['share_rate']);
        }
        if (!$this->isadmin && $ShareRate < $agent['share_rate']) {
            $this->response('0001', [], '占成比例不可下调');
        }

        $data = [];

        $add['Redme'] = $Memo;
        $add['phone'] = $Tel;
        $add['wechat'] = $weChatID;
        $add['bankNo'] = $BankCardID;
        $add['bankSon'] = $BankAddress;
        $add['realname'] = $Name;
        $add['nickname'] = $Name;

        $PassWd && $add['password'] = md5($PassWd . _CONF('salt'));
        $add['share_rate'] = max(0, $ShareRate);
        $this->User->update(['id' => $user['id']], $add);
        $add['game_user_id'] && $this->UserSettlement->update(['AgentID'=>$user['id'],'Status'=>[0,1],'is_delete'=>[0,1]],['UsrID'=>$add['game_user_id']]);
        $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '编辑账户' . ($ShareRate != $user['share_rate'] ? '占成' . $user['share_rate'] . '=>' . $ShareRate : '') . ',' . (isset($player['usrid']) ? '收益账户' . $user['game_user_id'] . '=>' . $player['usrid'] : ''));
        $this->response('0000', $data);
    }


    /**
     * @title  申请送分
     * @auth   true
     * @login  true
     * @menu   false
     * @button false
     * 2020/3/1 23:01
     */
    public function setScore_setApplyScore()
    {
        empty($this->token['is_req_give']) && $this->response('0001', [], '未获得申请送权限');
        $scoreNum = $this->request->param('scoreNum', '');
        if (empty($scoreNum) || !is_numeric($scoreNum)) {
            $this->response('0001', [], '请输入金额');
        }
        $userName = $this->request->param('userName', '');
        $user = $this->findPlayer($userName);
        if (empty($scoreNum)) {
            $this->response('0001', [], '金额为0');
        }
        $money = $scoreNum * $this->bl;
        $max = 99999 * $this->bl;
        if ($money > $max || $money < 1) {
            $this->response('0001', [], '超出设定范围');
        }
        $r = $this->ApplyLog->insert(['UsrID' => $user['usrid'], 'Money' => $money, 'AddTime' => time(), 'AgentID' => $this->token['id'], 'IP' => $this->request->get_client_ip(1)]);
        $r && $this->OperateLog->Add($this->token['id'], $user['usrid'], 0, $this->request->get_client_ip(1), '申请赠送:' . bcadd($scoreNum, 0, 2));
        $this->response($r != false ? '0000' : '0001');
    }

    /**
     * @title  审核送分
     * @auth   true
     * @login  true
     * @menu   false
     * @button false
     * 2020/3/2 14:47
     */
    public function setScore_AddApplyScore()
    {
        $this->needadmin();
        $LogID = $this->request->param('LogID', 0);
        $scoreNum = $this->request->param('Money', '');
        $type = $this->request->param('type', 0);
        empty($LogID) && $this->response('0001', [], '参数不完整');

        $apply = $this->ApplyLog->read(['LogID' => $LogID]);
        empty($apply['UsrID']) && $this->response('0001', [], '订单不存在');
        !empty($apply['Status']) && $this->response('0001', [], '订单已处理');
        if ($type == 1) {
            if (empty($scoreNum) || !is_numeric($scoreNum)) {
                $this->response('0001', [], '请输入金额');
            }
            $user = $this->Usr->read(['usrid' => $apply['UsrID']]);
            $money = $scoreNum * $this->bl;
            $max = 99999 * $this->bl;
            if ($money > $max || $money < 1) {
                $this->response('0001', [], '超出设定范围');
            }
            $this->ApplyLog->update(['LogID' => $LogID], ['Status' => $type, 'AdminID' => $this->token['id'], 'ApplyMoney' => $money, 'DoTime' => time()]);
            $r = $this->Gamebilog->Add($money, 29, $user['usrid'], $user['agent_id'], $this->request->get_client_ip(1), time(), $this->token['id']);
        } else {
            $r = $this->ApplyLog->update(['LogID' => $LogID], ['Status' => $type, 'AdminID' => $this->token['id'], 'ApplyMoney' => 0, 'DoTime' => time()]);
        }
        $this->response($r != false ? '0000' : '0001');
    }

    /**
     * 会员进分
     * setScore_setServerScore
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */

    /*
    public function setScore_setServerScore()
    {

        empty($this->roleInfo['fz']) AND $this->response('0001', [], '无加减分权限');
        $scoreNum = $this->request->param('scoreNum', '');
        if (empty($scoreNum) || !is_numeric($scoreNum)) {
            $this->response('0001', [], '请输入金额');
        }
        $userName = $this->request->param('userName', '');
        if (!$this->isadmin) {
            $this->response('0001', [], '管理员才可以操作');
        }

        $user = $this->findPlayer($userName);

        if (empty($scoreNum)) {
            $this->response('0001', [], '金额为0');
        }
        $money = $scoreNum * $this->bl;
        $max = 99999 * $this->bl;
        if ($money > $max || $money < -$max) {
            $this->response('0001', [], '超出设定范围');
        }

        if ($scoreNum > 0) {
            //目前在线上线需要踢人否则游戏内金额不一致
            $r = $this->Gamebilog->Add($money, 51, $user['usrid'], $user['agent_id'],  $this->request->get_client_ip(1), time(), $this->token['id']);
            if ($r == false) {
                $this->response('0001', [], $r['msg'] ? $r['msg'] : '操作失败');
            }
        } else {
            $user['online'] > 1 AND $this->response('0001', [], '玩家在游戏中不可减分');
            $time = time();
            $formData = [
                'usrid' => $user['usrid'],
                'times' => $time,
                'token' => getApiToken($time),
            ];
            $update_url = 'http://' . _CONF('gameServerIp') . ':9090/' . _CONF('kickPlayerApiUrl');
            post_api($update_url, $formData);
            \co::sleep(2);
            $user = $this->findPlayer($userName);
            bcadd($user['chips'], $user['bankchips']) < -$money AND $this->response('0001', [], '余额不足');
            if ($user['chips'] < 0-$money) { //需要从银行拿钱了
                $_money = -$money-$user['chips'];
                $user['chips'] = $user['chips']+$_money;
                $this->Usr->update(['usrid'=>$user['usrid']],['chips+'=>$_money,'bankchips-'=>$_money]);
            }
            $r = $this->Gamebilog->Sub($money, 52, $user['usrid'], $user['agent_id'],  $this->request->get_client_ip(1),  time(), $this->token['id']);
        }

        $this->response($r != false ? '0000' : '0001');
    }

    */

    /**
     * 会员进分
     * setScore_setServerScore
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function setScore_setServerScore()
    {
        $this->needadmin();
        empty($this->roleInfo['fz']) AND $this->response('0001', [], '无加减分权限');
        $scoreNum = $this->request->param('scoreNum', '');
        if (empty($scoreNum) || !is_numeric($scoreNum)) {
            $this->response('0001', [], '请输入金额');
        }
        $userName = $this->request->param('userName', '');
        if (!$this->isadmin) {
            $this->response('0001', [], '管理员才可以操作');
        }

        $user = $this->findPlayer($userName);
        if (empty($scoreNum)) {
            $this->response('0001', [], '金额为0');
        }
        $money = $scoreNum * $this->bl;
        $max = 99999 * $this->bl;
        if ($money > $max || $money < -$max) {
            $this->response('0001', [], '超出设定范围');
        }

        if ($scoreNum > 0) {
            $this->Gamebilog->Add($money, 51, $user['usrid'], $user['agent_id'], $this->request->get_client_ip(1), time(), $this->token['id']);
            $this->response('0000');
        } else {
            if($user['chips'] + $user['bankchips']+$money<0){
                $this->response('0001', [], '玩家余额不足');
            }
            if($user['online']>0){
                $this->response('0001', [], '玩家在线不能减分');
            }

            $r = $this->Gamebilog->Sub($money, 52, $user['usrid'], $user['agent_id'], $this->request->get_client_ip(1), time(), $this->token['id']);
            $this->response($r['success'] ? '0000' : '0001', [], '玩家游戏正在结算中请稍后再试');
        }
    }

    /**
     * 设置注册送分值
     * account_AdminSetRegSongNum
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_AdminSetRegSongNum()
    {
        $this->needadmin();
        $userName = $this->request->param('user', '');
        $num = $this->request->param('num', '');
        empty($num) AND $this->response('0001', [], '必须填写金额');
        $cond = [];
        $user = $this->default_user;
        if (!empty($userName)) {
            $user = $this->User->read_by_username($userName);
            $this->hspower($user['id']);
            $child_list = [];
            $this->agent_child_list($user['id'], $child_list);
            $agent_id = arrlist_values($child_list, 'id');
            $agent_id[] = $user['id'];
            $cond['id'] = $agent_id;
        }
        $max = 999;
        if ($num > $max || $num < 0) {
            $this->response('0001', [], '超出设定范围');
        }
        !$this->User->update($cond, ['reg_mark' => $num * $this->bl]) AND $this->response('0001', [], '注册送分值未变化');
        $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '修改【' . ($userName ? $user['username'] : '全局') . '】注册送分值【' . $num . '】');
        //empty($userName) AND $this->PlatformSetting->update(['name'=>'RegGiveSet'],['val'=>$num]);
        $this->response('0000');
    }

    /**
     * 后台修改密码
     * account_changePassword
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_changePassword()
    {

        $userName = $this->request->param('userName');

        $oldPassWd = trim($this->request->param('oldPassWd', ''));
        $Newpwd = trim($this->request->param('newPassWd', ''));
        $rePassWd = trim($this->request->param('rePassWd', ''));
        if ($Newpwd != $rePassWd) {
            $this->response('0001', [], '验证密码不一致');
        }

        $agent = !empty($userName) ? $this->User->read_by_username($userName) : $this->token;
        empty($agent) AND $this->response('0001', [], '账户不存在');
        $pwd = md5($Newpwd . _CONF('salt'));

        if (!empty($oldPassWd)) {
            $old_pwd = md5($oldPassWd . _CONF('salt'));
            if ($agent['password'] != $old_pwd) {
                $this->response('0001', [], '旧密码验证失败');
            }
        }

        $update_date['password'] = $pwd;
        !$this->User->update(['id' => $agent['id']], $update_date) AND $this->response('0001', [], '修改失败');
        $this->OperateLog->Add($this->token['id'], $agent['id'], 1, $this->request->get_client_ip(1), '修改密码');
        $this->response('0000');
    }


    /**
     * 后台修改安全码
     * account_changePassword
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_changeSafecode()
    {
        $userName = $this->request->param('userName');
        $oldPassWd = trim($this->request->param('oldPassWd', ''));
        $Newpwd = trim($this->request->param('newPassWd', ''));
        $rePassWd = trim($this->request->param('rePassWd', ''));
        if ($Newpwd != $rePassWd) {
            $this->response('0001', [], '验证安全码不一致');
        }

        if(strlen($Newpwd)!=4)
        {
            $this->response('0001', ['msg'=> '安全码长度为4位' ]);
        }
        $arr = [
            '1111',
            '1234',
            '4321',
            '2345',
            '0000',
            '2222',
            '3333',
            '4444',
            '5555',
            '6666',
            '7777',
            '8888',
            '9999',
        ];
        if(in_array($Newpwd,$arr))
        {
            $this->response('0001', ['msg'=> '安全码过于简单' ]);
        }
        $agent = !empty($userName) ? $this->User->read_by_username($userName) : $this->token;
        empty($agent) AND $this->response('0001', [], '账户不存在');
        if (!empty($oldPassWd)) {
            if ($agent['safe_code'] != $oldPassWd) {
                $this->response('0001', [], '旧安全码验证失败');
            }
        }

        $update_date['safe_code'] = $Newpwd;
        $this->session->sess['user']['safe_code'] = $Newpwd;
        !$this->User->update(['id' => $agent['id']], $update_date) AND $this->response('0001', [], '修改失败');
        $this->OperateLog->Add($this->token['id'], $agent['id'], 1, $this->request->get_client_ip(1), '修改安全码');
        $this->response('0000');
    }

    /**
     * 踢出游戏
     * account_quiteGame
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_quiteGame()
    {
        $this->needadmin();
        $user = $this->request->param('user', '');
        empty($user) AND $this->response('0001', [], '缺少参数');
        $player = $this->findPlayer($user);
        $time = time();
        $formData = [
            'usrid' => $player['usrid'],
            'times' => $time,
            'token' => getApiToken($time),
        ];
        $update_url = 'http://' . _CONF('gameServerIp') . ':9090/' . _CONF('kickPlayerApiUrl');
        $ret = post_api($update_url, $formData);
        empty($ret['success']) AND $this->response('0001', [], $ret['msg'] ? $ret['msg'] : '操作失败');
        $this->OperateLog->Add($this->token['id'], $player['usrid'], 0, $this->request->get_client_ip(1), '踢玩家下线');
        //$this->Usr->update(['usrid'=>$player['usrid']],['disable'=>1]);
        $this->response('0000');
    }

    /**
     * 修改子账户密码
     * account_setManagerPwd
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_setManagerPwd()
    {
        $this->needadmin();
        $Newpwd = $this->request->param('Newpwd', '');
        $userName = $this->request->param('userName', '');
        $agent = $this->User->read_by_username($userName);
        empty($agent) AND $this->response('0001', [], '账户不存在');
        $my_pwd = md5($Newpwd . _CONF('salt'));
        $my_pwd == $agent['password'] AND $this->response('0001', [], '密码重复，未变更');
        !$this->User->update(['id' => $agent['id']], ['password' => $my_pwd]) AND $this->response('0001', [], '修改失败');
        $this->OperateLog->Add($this->token['id'], $agent['id'], 1, $this->request->get_client_ip(1), '修改登陆密码');
        $this->response('0000');
    }

    /**
     * 短信赠送
     * account_setSMSInfo_Num
     *
     * @auth  true
     * @login true
     * @menu  false
     * @throws \Exception
     */
    public function account_setSMSInfo_Num()
    {
        $this->response('0001',[],'短信送关闭');
        $user = $this->request->param('user');
        $setNum = max(0, $this->request->param('setNum', 0));
        empty($setNum) AND $this->response('0001', [], '赠送金额不能为空');
        $sms_largess = explode(',', _CONF('SmsLargess'));
        !in_array($setNum, (array)$sms_largess) AND $this->response('0001', [], '赠送金额不正确！');
        $player = $this->findPlayer($user);
        $key = 'sms_lock_' . $player['usrid'];
        $islock = $this->User->CacheGet($key);
        $time = time();
        if (!empty($islock['time'])) {
            $this->response('0001', [], '操作太快');
        }
        $this->User->CacheSet($key, ['time' => $time], 60);
        $issend = $this->Gamebilog->find_one(['UsrID' => $player['usrid'], 'Type' => 50], ['LogID' => -1]);
        if ($issend['AddTime'] >= strtotime(date('Y-m-d 00:00:00'))) {
            $this->response('0001', [], '今天已赠送');
        }
        $agent = $this->User->read_by_id($player['agent_id']);
        empty($agent['id']) AND $this->response('0001', [], '上级代理有误');
        if (!$this->isadmin) {
            $this->default_user['id'] != $player['agent_id'] AND $this->response('0001', [], '直属玩家才可赠送');
        }

        if (empty($agent['is_sms_give']) || $agent['sms_set'] < 1) {
            $this->User->CacheDel($key);
            $this->response('0001', [], '上级代理可赠送次数不足');
        }
        $rlog = $this->Gamebilog->read(['UsrID' => $player['usrid'], 'Type' => 50, 'DoAt' => 0]);
        if (!empty($rlog['LogID'])) {
            $this->User->CacheDel($key);
            $this->response('0001', [], '玩家有赠送尚未领取');
        }
        $apiName = 'HonglianSms';
        $ret = $this->SmsCode->send($apiName, 'smsSong', $player['openid'], ['vars' => ['code' => $setNum]]);
        if (empty($ret['success'])) {
            $this->response('0001', [], '赠送短信发送失败');
        }
        $this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '操作短信送' . $player['usrid'] . ' 金额:' . $setNum);
        $this->User->update(['id' => $agent['id']], ['sms_set-' => 1]);
        $this->Gamebilog->Add($setNum * $this->bl, 50, $player['usrid'], $agent['id'], $this->request->get_client_ip(1), 0, $this->token['id']);
        $this->User->CacheDel($key);
        $this->response("0000");
    }


    /**
     * 添加后台白名单
     *
     * @throws \Exception
     */
    public function account_addAdminIp()
    {
        $this->needadmin(1);
        $ip = $this->request->param('ip', '');
        $adminId = $this->request->param('admin', '');
        if (!$ip) {
            $this->response('0001', [], 'IP不能为空');
        }
        if (!$adminId) {
            $this->response('0001', [], '账户不能为空');
        }
        $note = $this->request->param('note', '');
        if (!$note) {
            $this->response('0001', [], '备注不能为空');
        }
        $user = $this->User->read(['username'=>$adminId]);
        empty($user['id']) && $this->response('0001', [], '账户不能为空');
        $data = [
            'ip' => $ip,
            'adminId'=>$user['id'],
            'note' => $note,
            'times' => time(),
        ];
        !$this->AdminIpWhiteList->insert($data) AND $this->response('0001', [], '添加后台IP');
        $this->OperateLog->Add($this->token['id'], $user['id'], 1, $this->request->get_client_ip(1), '添加【'.$user['username'].'】IP白名单【' . $ip . '】');
        $this->response('0000');
    }

    /**
     * 删除后台IP
     *
     * @throws \Exception
     */
    public function account_delAdminIp()
    {
        $this->needadmin(1);
        $id = $this->request->param('id', '');
        if (!$id) {
            $this->response('0001', [], 'ID不能为空');
        }
        !$this->AdminIpWhiteList->delete(['id' => $id]) AND $this->response('0001', [], '删除IP成功');
        $this->OperateLog->Add($this->token['id'], 0, 1, $this->request->get_client_ip(1), '删除后台IP白名单【' . $id . '】');
        $this->response('0000');
    }


    /**
     * 移除白名单
     *
     * @throws \Exception
     */
    public function account_delSuperControl()
    {
        $this->needadmin(1);
        $id = $this->request->param('id', '');
        if (!$id) {
            $this->response('0001', [], 'ID不能为空');
        }
        !$this->SuperControlList->delete(['player_id' => $id]) AND $this->response('0001', [], '删除单控设置成功');
        $this->Usr->update(['usrid' => $id], ['super_control' => 0, 'sup_control_val' => 0]);
        $this->OperateLog->Add($this->token['id'], 0, 1, $this->request->get_client_ip(1), '删除后台IP白名单【' . $id . '】');
        $this->response('0000');
    }

    /**
     * 移除超级单控设置
     *
     * @throws \Exception
     */
    public function account_delSuperItemControl()
    {
        $this->needadmin(1);
        $id = $this->request->param('id', '');
        if (!$id) {
            $this->response('0001', [], 'ID不能为空');
        }
        $res = $this->SuperControlList->read(['id' => $id]);
        $count = $this->SuperControlList->count(['player_id' => $res['player_id']]);
        if ($count < 2) {
            $this->response('0001', [], '至少保留一条配置');
        }
        !$this->SuperControlList->delete(['id' => $id]) AND $this->response('0001', [], '单控设置删除成功');
        $this->OperateLog->Add($this->token['id'], 0, 1, $this->request->get_client_ip(1), '删除单控设置【' . $id . '】');
        $this->response('0000', ['type' => 1]);
    }


    /**
     * 添加超控设置
     *
     * @throws \Exception
     */
    public function account_addSuperControlItem()
    {
        $this->needadmin(1);
        $startVal = $this->request->param('startVal', '');
        if (empty($startVal)) {
            $this->response('0001', [], '开始值不能为空');
        }

        $endVal = $this->request->param('endVal', '');
        if (empty($startVal)) {
            $this->response('0001', [], '结束值不能为空');
        }
        $code = $this->request->param('code', '');
        if (empty($code)) {
            $this->response('0001', [], '标识不能为空');
        }
        $val = $this->request->param('val', '');
        if (empty($val)) {
            $this->response('0001', [], '控制值不能为空');
        }
        $id = $this->request->param('user', '');
        $data = [
            'start_val' => $startVal,
            'end_val' => $startVal,
            'code' => $code,
            'player_id' => $id,
            'times' => time(),
        ];

        !$this->SuperControlList->insert($data) AND $this->response('0001', [], '添加成功');
        $this->OperateLog->Add($this->token['id'], 0, 1, $this->request->get_client_ip(1), '添加超级单控【' . $id . '】');
        $this->response('0000');
    }


}