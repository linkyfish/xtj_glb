<?php

namespace Com;

use Ctrl\GameController;
use Server\Bt;

Class Devops extends GameController
{

    public function Index()
    {
        $this->needadmin(1);
        $types = [
            1 => [
                'name' => '服务启停',
                'type' => 'game',
            ],
            2 => [
                'name' => '下载设置',
                'type' => 'app_down',
            ],
            4 => [
                'name' => '短信接口',
                'type' => 'sms',
            ],
            6 => [
                'name' => '备用域名切换',
                'type' => 'domain',
            ],
            9 => [
                'name' => '其他设置',
                'type' => 'other',
            ]
        ];

        $domainList = $this->SysDomain->select();
        $domainData = [];
        foreach ($domainList as $item) {
            switch ($item['type']) {
                case "down":
                    $domainData['down'][] = $item;
                    break;
                case "reg":
                    $domainData['reg'][] = $item;
                    break;
                case "agent":
                    $domainData['agent'][] = $item;
                    break;
                case "admin":
                    $domainData['admin'][] = $item;
                    break;
                case "cash":
                    $domainData['cash'][] = $item;
                    break;
                default:
                    break;
            }
        }

        $downStatus = _CONF('downloadPageOpen');
        $androidUrl = _CONF('apk_down_url');
        $iosUrl = _CONF('ios_down_url');
        $adsms = _CONF('adsms');
        $commonsms = _CONF('commonsms');
        $smsChannelList = $this->SmsConfig->select();
        $smsList = [];
        foreach ($smsChannelList as $item) {
            if ($item['IsCommon']) {
                $smsList['common'][] = $item;
            }
            if ($item['IsPromotion']) {
                $smsList['promotion'][] = $item;
            }
        }

        $cashUrl = _CONF('CashUrl');

        $regUrl = _CONF('CashUrl');


        return $this->View(get_defined_vars());
    }

    public function Index_kickUser()
    {
        $userName = $this->request->param('username', '');
        $user = $this->findPlayer($userName);
        if (!$user) {
            $this->response('0001', '用户不存在');
        }
        $this->Usr->update(['usrid' => $user['usrid']], ['online' => 0, 'roomid' => 0]);
        $this->response('0000', $user);
    }


    public function Index_editDown()
    {
        $android_url = $this->request->param('android_url', '');
        $ios_url = $this->request->param('ios_url', '');
        $this->PlatformSetting->update(['name' => 'apk_down_url'], ['val' => $android_url]);
        $this->PlatformSetting->update(['name' => 'ios_down_url'], ['val' => $ios_url]);
        $this->response('0000');
    }

    public function Index_downOpenSet()
    {
        $status = $this->request->param('status', '1');
        $this->PlatformSetting->update(['name' => 'downloadPageOpen'], ['val' => $status]);
        $this->response('0000');
    }

    public function Index_config()
    {
        $this->needadmin(1);
        $type = $this->request->param('type', 'system');
        empty($type) AND $type = 'system';
        $data = $this->PlatformSetting->select(['setting_type' => $type, 'allow_dev' => 1]);
        $this->response('0000', ['results' => $data]);
    }

    public function Index_edit()
    {
        $this->needadmin(1);
        $id = $this->request->param('id', 0);
        $value = $this->request->param('value', '');
        empty($id) AND $this->response('0001', [], '编号不存在');
        $this->PlatformSetting->update(['id' => $id], ['val' => $value]);
        $this->PlatformSetting->CacheDel('PlatformSetting');
        $this->response('0000');
    }


    public function Index_editSmsChannel()
    {
        $type = $this->request->param('sms_type', '');
        $val = $this->request->param('val', '');
        $setting_key = '';
        switch ($type) {
            case  'adsms':
                $setting_key = 'adsms';
                break;
            case  'commonsms':
                $setting_key = 'commonsms';
                break;
        }
        $this->PlatformSetting->update(['name' => $setting_key], ['val' => $val]);
        $this->response('0000');
    }


    public function __addFirePort($port)
    {
        $formData = [
            'port' =>$port,
        ];
        $apiPort = '51666';
        $serverIp = _CONF('gameServerIp');
        $update_url = 'http://' . $serverIp . ':' . $apiPort . '/closePort';
        $response = post_api($update_url, $formData);
    }


    public function __removeFirePort($port)
    {
        $formData = [
            'port' =>$port,
        ];
        $apiPort = '51666';
        $serverIp = _CONF('gameServerIp');
        $update_url = 'http://' . $serverIp . ':' . $apiPort . '/openPort';
        $response = post_api($update_url, $formData);
    }

    public function  Index_editConfig()
    {
        $this->response('0000');
        /*
        $val = $this->request->param('val', '');
        $configName= $this->request->param('configName', '');
        $this->PlatformSetting->update(['name' => $configName, 'val' =>$val]);
        $this->response('0000');
        */
    }

    public function  setGameServerIp()
    {
        $val = $this->request->param('ip', '');

    }





}
