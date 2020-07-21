<?php
/**
 * Created by PhpStorm.
 * User: yinhanmin
 * Date: 2019-03-09
 * Time: 08:48
 */

namespace Ashx;


use Ctrl\GameController;

class Editfield extends GameController
{

	public function editField_ModifyUserInfo()
	{
		//$this->needadmin();
		$field = $this->request->param('field', '');
		$canEdit = true;
        if(empty($this->roleInfo['bj']) && $field != 'UserShareRate')
        {
            $canEdit = false;
        }
        if($this->roleInfo['xgbz'])
        {
            $canEdit = true;
        }
        if(!$canEdit)
        {
            $this->response('0001', [], '无权编辑资料');
        }

		$id = $this->request->param('id', '');
		$value = $this->request->param('value', '');
		$_id = str_replace('player_', '', $id);
		$data['NewValue'] = $value;
		if ($_id != $id) {
			$user = $this->findPlayer($_id);
			switch ($field) {
				case 'wechatID':
					$updata['wechat'] = $value;
					break;
				case 'Redme':
					$updata['note'] = $value;
					break;
				case 'Name':
					$updata['realname'] = $value;
					break;
				case 'BankCardID':
					$updata['bankcard'] = $value;
					break;
				case 'BankAddress':
					$updata['bankSon'] = $value;
					break;
				case 'Tel':
					$updata['phone'] = $value;
					break;
			}
			//修改备注的权限
            if($this->roleInfo['xgbz'])
            {
                if($field =='Redme')
                {
                    $updataNote['note'] = $updata['note'];
                    $updata = $updataNote;
                }

            }

			$updata AND !$this->Usr->update(['usrid' => $user['usrid']], $updata) AND $this->response('0001', [], '更新失败');
		} else {
			$agent = $this->User->read_by_username($id);
			if (!$agent['id']) {
				$this->response('0001', [], '更新失败');
			}


			if ($field == 'UserShareRate') {
				if ($value < 0) {
					$this->response('0001', [], '比例小于0');
				} else if ($value > 100) {
					$this->response('0001', [], '比例大于100');
				}
				if ($agent['parent_id']) {
					$Parent_agent = $this->User->read_by_id($agent['parent_id']);
					if ($Parent_agent['share_rate'] && $Parent_agent['share_rate'] < $value) {
						$this->response('0001', [], '比例不能高于上级代理');
					}
				}

				$son_agent = $this->User->find_one(['parent_id' => $agent['id']], ['share_rate' => -1]);
				if (!empty($son_agent['share_rate']) && $son_agent['share_rate'] > $value) {
					$this->response('0001', [], '比例不能低于下级代理 ' . $son_agent['share_rate']);
				}
				if (!$this->isadmin && $value < $agent['share_rate']) {
					$this->response('0001', [], '占成比例不可下调');
				}

				$updata['share_rate'] = $value;
				!$this->User->update(['id' => $agent['id']], $updata) AND $this->response('0001', [], '更新失败');
			} else if ($field == 'wechatID') {
				$updata['wechat'] = $value;
				!$this->User->update(['id' => $agent['id']], $updata) AND $this->response('0001', [], '更新失败');
			} else if ($field == 'Redme') {
				$updata['Redme'] = $value;
				!$this->User->update(['id' => $agent['id']], $updata) AND $this->response('0001', [], '更新失败');
			} else if ($field == 'BankCardID') {
				$updata['bankNo'] = $value;
				!$this->User->update(['id' => $agent['id']], $updata) AND $this->response('0001', [], '更新失败');
			} else if ($field == 'BankAddress') {
				$updata['bankSon'] = $value;
				!$this->User->update(['id' => $agent['id']], $updata) AND $this->response('0001', [], '更新失败');
			} else if ($field == 'Name') {
				$updata['nickname'] = $value;
				!$this->User->update(['id' => $agent['id']], $updata) AND $this->response('0001', [], '更新失败');
			} else {
				$this->response('0001', [], '更新失败');
			}
		}
		$this->response('0000', $data);
	}


	public function editField_editgamelog()
	{
		$this->needadmin(1);
		$id = $this->request->param("id", 0);
		$field = $this->request->param('field', '');
		$value = $this->request->param('value', '');
		$this->MoneyTrans->update(['id' => $id], [$field => $value]);
		$data['NewValue'] = $value;
		$this->response('0000', $data);
	}

	public function editField_repairlog()
	{
		$this->needadmin(1);
		$id = $this->request->param("LogID", 0);
		$log = $this->Gamebilog->read(['LogID' => $id]);
		empty($log['LogID']) && $this->response('0001', [], '日志不存在');
		$arr = [
			'chips' => $log['Money'],
			'usrid' => $log['UsrID'],
			'style' => $log['Type'],
			'transtype' => $this->MoneyTrans->Type[$log['Type']].'B',
			'action_id' => $log['AdminID'],
			'action_time' => $log['DoAt'],
			'BeginBlance' => $log['AfterMoney'] - $log['Money'],
			'EndBlance' => $log['AfterMoney'],
            'action_ip' => long2ip($log['IP']),
			'times' => date("Y-m-d H:i:s", $log['AddTime']),
		];
		$r = $this->MoneyTrans->insert($arr);
		$this->response($r == false ? '0001' : '0000', $arr, $r == false ? "插入失败" : "操作成功");
	}


	public function editField_edit_rank_virtual()
	{
		$this->needadmin();
		$id = $this->request->param("id");
		$field = $this->request->param('field', '');
		$value = $this->request->param('value', '');
		!$this->PaymentCustom->update(['ID' => $id], [$field => $value]) && $this->response('0001', [], '修改失败');
		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '修改排行榜【' . $id . '】' . $field . '=>' . $value);
		$data['NewValue'] = $value;
		$this->response('0000', $data);
	}


	public function editField_edit_custom()
	{
		$this->needadmin();
		$id = $this->request->param("id");
		$field = $this->request->param('field', '');
		$value = $this->request->param('value', '');
		!$this->Custom->update(['ID' => $id], [$field => $value]) && $this->response('0001', [], '修改失败');

		$update_url = 'http://' . _CONF('gameServerIp') . ':9090' . '/' . _CONF('updateShopPaymentClientUrl');
		//请求服务器刷新内存
		http_get_api($update_url,[]);
		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '修改商城客服【' . $id . '】' . $field . '=>' . $value);
		$data['NewValue'] = $value;
		$this->response('0000', $data);
	}

	public function editField_edit_feedback()
	{
		$this->needadmin();
		$id = $this->request->param("id");
		$field = $this->request->param('field', '');
		$value = $this->request->param('value', '');
		!$this->FeedBack->update(['id' => $id], [$field => $value]) && $this->response('0001', [], '修改失败');
		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '修改玩家反馈【' . $id . '】' . $field . '=>' . $value);
		$data['NewValue'] = $value;
		$this->response('0000', $data);
	}


	public function editField_add_rank_virtual()
	{
		$this->needadmin();
		$id = $this->request->param("id");
		$data['UserName'] = $this->request->param('player_name', '');
		$data['Signature'] = $this->request->param('qianming', '');
		$data['WeChatID'] = $this->request->param('wechat', '');
		$data['MoneyNum'] = $this->request->param('value', '');
		$data['RankNo'] = $this->PaymentCustom->Max('RankNo', []) + 1;
		!$this->PaymentCustom->insert($data) && $this->response('0001', [], '添加失败');
		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '新增排行榜【' . $id . '】');
		$this->response('0000');
	}

	public function editField_add_custom()
	{
		$this->needadmin();
		$id = $this->request->param("id");
		$data['UserName'] = $this->request->param('player_name', '');
		$data['Signature'] = $this->request->param('qianming', '');
		$data['WeChatID'] = $this->request->param('wechat', '');
		$data['MoneyNum'] = $this->request->param('value', '');
		$data['head_img'] = $this->request->param('head_img', '');
		$data['RankNo'] = $this->Custom->Max('RankNo', []) + 1;
		!$this->Custom->insert($data) && $this->response('0001', [], '添加失败');
		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '新增商城客服【' . $id . '】');
		$this->response('0000');
	}

	public function editField_editField_room()
	{
		$this->needadmin();
		$id = $this->request->param("id");
		$field = $this->request->param('field', '');
		$value = $this->request->param('value', '');

		!$this->Game->update(['id' => $id], [$field => $value]) && $this->response('0001', [], '修改失败');
		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '修改奖池【' . $id . '】' . $field . '=>' . $value);
		//API
		$this->response('0000');
	}

	public function editField_Disable_rank_virtual()
	{
		$this->needadmin(1);
		$id = $this->request->param("id");
		$Payment = $this->PaymentCustom->read(['ID' => $id]);
		!$this->PaymentCustom->update(['ID' => $id], ['Status' => $Payment['Status'] == 1 ? 0 : 1]) && $this->response('0001', [], '修改失败');
		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '修改排行榜【' . $id . '】' . $Payment['Status'] == 1 ? '禁用' : '启用');
		$this->response('0000');
	}

	public function editField_Disable_custom()
	{
		$this->needadmin(1);
		$id = $this->request->param("id");
		$Payment = $this->Custom->read(['ID' => $id]);
		!$this->Custom->update(['ID' => $id], ['Status' => $Payment['Status'] == 1 ? 0 : 1]) && $this->response('0001', [], '修改失败');
		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '修改商城客服【' . $id . '】' . $Payment['Status'] == 1 ? '禁用' : '启用');
		$time = time();
		$formData = [
			'times' => $time,
			'token' => getApiToken($time),
		];
		$update_url = 'http://' . _CONF('gameServerIp') . ':9090/' . _CONF('updateShopPaymentClientUrl');
		post_api($update_url, $formData);
		$this->response('0000');
	}


	/**
	 * 更新公告
	 *
	 * @throws \Exception
	 */
	public function editField_noticeEdit()
	{

		$this->needadmin();
		$id = $this->request->param("id");
		$field = $this->request->param('field', '');
		$value = $this->request->param('value', '');
		$type = $this->request->param('type', '');
		if (!empty($type)) {
			!$this->AdCopy->update(['id' => $id], [$field => $value]) && $this->response('0001', [], '修改失败');
			$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '修改跑马灯【' . $id . '】' . $field . '=>' . $value);
			$data['NewValue'] = $value;
			$this->response('0000', $data);
		} else {
			!$this->GmNotice->update(['id' => $id], [$field => $value]) && $this->response('0001', [], '修改失败');
			$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '修改公告【' . $id . '】' . $field . '=>' . $value);
			$data['NewValue'] = $value;
			$this->response('0000', $data);

		}

	}

	/**
	 * 更新奖池
	 *
	 * @throws \Exception
	 */
	public function editField_room()
	{

        $this->needadmin();
        $id = $this->request->param("id");
        $field = $this->request->param('field', '');
        $value = $this->request->param('value', '');
        $BeforeValue = $this->request->param('BeforeValue', '');
        $roomInfo = $this->RoomBank->read(['roomID' => $id]);
        $serverIP = $roomInfo['server_ip'];
        $update_url = 'http://' . $serverIP . ':' . $roomInfo['port'] . '/UpdateRewardBank';
        $formData = [
            'gameid' => $id,
            'bank' => $value,
        ];
        $formData['banktag'] =0;
        if(in_array($id,$this->Game->byGame))
        {
            switch ($field)
            {
                case 'bigBank':
                    $formData['banktag'] = 2;
                    break;
                case 'smallBank':
                    $formData['banktag'] = 3;
                    break;
                case 'systemStock':
                    $formData['banktag'] =1;
                    break;
                case 'smallUserBigBank':
                    $formData['banktag'] =4;
                    break;
                case 'smallUserSmallBank':
                    $formData['banktag'] =5;
                    break;
            }
        }
        else{
            switch ($field)
            {
                case 'systemStock':
                    $formData['banktag'] =1;
                    break;
            }
            if($id >= 300041 &&  $id  <= 320073)
            {
                switch ($field)
                {
                    case 'bigBank':
                        $formData['banktag'] = 2;
                        $formData['bank'] = $value;
                        break;
                }
            }
        }

        $time = time();
        $timeCut = substr($time,strlen($time)-5,5) ;
        //api_salt => oiea932na
        $sign = md5($formData['gameid'].$formData['bank']._CONF('api_salt').$timeCut);
        $formData['sign'] = $sign;
        $formData['time'] = $time;
        $response = post_api($update_url, $formData);

        if ($response['success'] != 1) {
            $this->response('0001', [], '奖池更新失败' . $response['msg']);
        }
        $this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '更新奖池【' . $BeforeValue . '】' . $field . '=>' . $value);
        $data['NewValue'] = $value;
        $this->response('0000', $data);

	}

	public function editField_resetAllLine()
    {
        $this->needadmin();
        $this->response('0000', []);
    }

	/**
	 * 更新支付渠道状态
	 *
	 * @throws \Exception
	 */
	public function editField_changepaychannelstatus()
	{

		$this->needadmin();
		$id = $this->request->param("id");
		$field = $this->request->param('field', '');
		$type = $this->request->param('type', '');
		$channelInfo = $this->PayConfig->read(['ID' => $id]);
		if ($channelInfo['Channel' . $type] == 1) {
			$data['Channel' . $type] = 0;
		} else {
			$data['Channel' . $type] = 1;
		}
		$this->PayConfig->update(['ID' => $id], $data);
		$this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '更新支付状态【' . $channelInfo['Name'] . '】为' . $data['Channel' . $type] == 1 ? '启用' : '禁用');
		$this->response('0000', $data);

	}


}
