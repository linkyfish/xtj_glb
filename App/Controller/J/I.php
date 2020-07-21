<?php

namespace J;

use Api\Sms;
use Ctrl\Controller;

Class I extends Controller
{
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
	public function Index(){
    	$this->response('0000',[],'','http://'.createCode(5),302);
	}

    public function A()
    {
        $get = $this->request->param(3);
		$dc = xn_decrypt($get,1);

		if(empty($dc)){
			$this->response('0001',[],'','http://'.createCode(5),302);
		}
		$url='http://' . createCode(5) .'.'. _CONF('JumpUrl02') . '/' . createCode(5) . '.html';
		$sm4 = new \SM4();
		$html ='<form action="' . $url . '" name="myfrom" id="myform" method="get" ><input type="hidden" name="item_id" value="' . $get . '" /></form>';
		$html=$sm4->encrypt($get,$html);
		$str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>加载中</title></head><body>安全检查,请稍候..<div id="body" style="display:none;">'.$html.'</div><div id="item" style="display:none">'.$get.'</div><script src="../../../js/sm4.js"></script><script> document.getElementById("body").innerHTML=window.SM4.decode({input:document.getElementById("body").innerHTML,key:document.getElementById("item").innerHTML}) ;</script><script>document.getElementById("myform").submit();</script></body></html>';
		return $str;
    }

    public function B(){
    	//http://127.0.0.1:8364/j/i/b/bBPvH.html?item_id=dpcTtMknWDTj3Yhq09AAnQ_3d_3d
        $get = $this->request->param('item_id');
        $data =xn_decrypt($get,1);
		if(empty($data)){
			$this->response('0001',[],'','http://'.createCode(5),302);
		}
		$k='';
		$v='';
		if(str_replace('code_','',$data)!=$data){
			//注册地址
			$url = _CONF('ShareUrl');
			$f='code';
			$v=str_replace('code_','',$data);
		}else{
			list($type,$f,$v) = explode('/',$data);
			$key = ucfirst($type).'Url';
			$url = _CONF($key);
			$f&&$v && $url.='/?'.$f.'='.$v;
		}
		$sm4 = new \SM4();
		$html ='<form action="' . $url . '" name="myfrom" id="myform" method="get" ><input type="hidden" name="'.$f.'" value="'.$v.'"/></form>';
		$html=$sm4->encrypt($get,$html);
		$str = '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml"><head><meta http-equiv="Content-Type" content="text/html; charset=utf-8" /><title>加载中</title></head><body>安全检查,请稍候..<div id="body" style="display:none">'.$html.'</div><div id="item" style="display:none">'.$get.'</div><script src="../../../js/sm4.js"></script><script>document.getElementById("body").innerHTML=window.SM4.decode({input:document.getElementById("body").innerHTML,key:document.getElementById("item").innerHTML}) ;</script><script>document.getElementById("myform").submit();</script></body></html>';
		return $str;
		//$this->response('0000',[],$url,$url,302);
    }

}
