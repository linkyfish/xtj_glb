<?php

namespace Com;

use Ctrl\GameController;

Class Sys_config extends GameController {

    public function Index () {
		if($this->token['RoleID'] != 1){
			$this->response('0001',[],'没权限');
		}
        $types = [
            0=>[
                'name'=>'游戏设置',
                'type'=>'game',
            ],
            1=>[
                'name'=>'游戏服务器接口',
                'type'=>'game_server_api',
            ],
            2=>[
                'name'=>'APP设置',
                'type'=>'app_down',
            ],
            3=>[
                'name'=>'公告设置',
                'type'=>'notice',
            ],
            4=>[
                'name'=>'短信设置',
                'type'=>'sms',
            ],
            6=>[
                'name'=>'跳转域名设置',
                'type'=>'jump',
            ],
            7=>[
                'name'=>'二维码设置',
                'type'=>'qrcode',
            ],
            8=>[
                'name'=>'支付相关设置',
                'type'=>'payment',
            ],
            9=>[
                'name'=>'客服设置',
                'type'=>'client',
            ],
            10=>[
                'name'=>'其他API设置',
                'type'=>'other_api',
            ],
        ];
        return $this->View(get_defined_vars());
    }

    public function Index_config(){
		if($this->token['RoleID'] != 1){
			$this->response('0001',[],'没权限');
		}
        $type =$this->request->param('type','system');
        empty($type) AND $type='system';
        $data = $this->PlatformSetting->select(['setting_type'=>$type]);
        $this->response('0000',['results'=>$data]);
    }
    public function Index_edit(){
		if($this->token['RoleID'] != 1){
			$this->response('0001',[],'没权限');
		}
        $id =$this->request->param('id',0);
        $value=$this->request->param('value','');
        empty($id) AND $this->response('0001',[],'编号不存在');
        $this->PlatformSetting->update(['id'=>$id],['val'=>$value]);
        $this->PlatformSetting->CacheDel('PlatformSetting');
        $this->response('0000');
    }
}
