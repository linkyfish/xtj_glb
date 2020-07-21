<?php

namespace Api;
use Api\Interfaces\CommonSms;
use Co;
use Ctrl\Controller;
Class Sms extends  Controller
{

    public function Index()
    {
        return 6666;
    }

    public function sendOld()
    {

        $un = $this->PlatformSetting->read(['name'=>'sms_265_un'])['val'];
        $pw = $this->PlatformSetting->read(['name'=>'sms_265_pw'])['val'];
        $sms_url = $this->PlatformSetting->read(['name'=>'sms_265_url'])['val'];
        $phone = $this->request->param('phone','');
        if(empty($phone))
        {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'手机号码不能为空']);
        }
        $phone = str_replace('+86','',$phone);
        $code =  rand(10000, 99999);
        $id = $this->SmsCode->insert([
            'code'=>$code,
            'phone'=>$phone,
            'create_time'=>time(),
        ]);
        $content = sprintf("【大洋娱港】您正在进行手机验证，验证码%s，有效期15分钟。", $code);
        $data = [
            'account'=>$un,
            'password'=>$pw,
            'phone'=>$phone,
            'report'=>true,
            'msg'=>$content
        ];

        list(, , $host, $remote_server) = explode('/', $sms_url);
        list($host, $port) = explode(':', $host);
        $cli = new \Swoole\Coroutine\Http\Client($host, 80);
        $cli->setHeaders([
            'Host' => $host,
            'Accept-Encoding' => 'gzip',
            'Accept' => 'text/html,application/xhtml+xml,application/xml,application/json',
        ]);

        $cli->set(['timeout' => 60]);
        try{
            $cli->post('/msg/send/json', json_encode($data));
            $response = $cli->body;
            $obj = json_decode($response);
            $cli->close();
            if($obj->code==0)
            {
                return json_encode(['code'=>0,'success'=>true,'msg'=>'短信发送成功']);
            }
            return json_encode(['code'=>0,'success'=>false,'msg'=>'短信发送失败']);

        }
        catch (\Exception $ex)
        {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'短信发送失败']);
        }
    }


    public function send()
    {
        $phone = $this->request->param('phone','');
        if(empty($phone))
        {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'手机号码不能为空']);
        }
        $phone = str_replace('+86','',$phone);
        $code =  rand(10000, 99999);
        $id = $this->SmsCode->insert([
            'code'=>$code,
            'phone'=>$phone,
            'create_time'=>time(),
        ]);
        $data['tpl'] = 'normal';
        $data['phone'] = $phone;
        $data['vars'] = ['code'=>$code];
        $r = $this->_smsGatewaySend($data);
        return  json_encode($r);

    }

    public  function  smstest()
    {
        include_once _include(__APPDIR__ . 'Controller/Api/Interfaces/SmsBase.php');
        //        $smsname = _CONF('commonsms');
        $smsname = $this->request->param('sms','HexintongSms');;
        $tpl = $this->request->param('tpl','normal');
        $phone = $this->request->param('phone','');
        $code = $this->request->param('code','');

        if (is_file(__APPDIR__ . 'Controller/Api/Interfaces/' . $smsname . '.php')) {
            include_once _include(__APPDIR__ . 'Controller/Api/Interfaces/' . $smsname . '.php');
            $contor = '\\Api\\Interfaces\\' . $smsname;
            $sms = new $contor($this);
            $smsData['code'] = $code;
            $smsData['vars'] = ['code'=>$code];
            $ret = $sms->send($smsData,$phone,$tpl);
            return $ret['result'];

        } else {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'短信发送失败']);
        }
    }
    public function  _smsGatewaySend($data)
    {
        include_once _include(__APPDIR__ . 'Controller/Api/Interfaces/SmsBase.php');
        $smsname = _CONF('commonsms');
        $tpl =  $data['tpl'] ;
        $phone =  $data['phone'] ;
        $code =  $data['code'] ;
        if (is_file(__APPDIR__ . 'Controller/Api/Interfaces/' . $smsname . '.php')) {
            include_once _include(__APPDIR__ . 'Controller/Api/Interfaces/' . $smsname . '.php');
            $contor = '\\Api\\Interfaces\\' . $smsname;
            $sms = new $contor($this);
            $smsData['code'] = $code;
            $smsData['vars'] = $data['vars'];
            $ret = $sms->send($smsData,$phone,$tpl);
            return $ret;

        } else {
            return ['code'=>0,'success'=>false,'msg'=>'短信渠道未开启'];
        }
    }

    private function IsMobile($phone_number)
    {
        //@2017-11-25 14:25:45 https://zhidao.baidu.com/question/1822455991691849548.html
        //中国联通号码：130、131、132、145（无线上网卡）、155、156、185（iPhone5上市后开放）、186、176（4G号段）、175（2015年9月10日正式启用，暂只对北京、上海和广东投放办理）,166,146
        //中国移动号码：134、135、136、137、138、139、147（无线上网卡）、148、150、151、152、157、158、159、178、182、183、184、187、188、198
        //中国电信号码：133、153、180、181、189、177、173、149、199
        $g = "/^1[34578]\d{9}$/";
        $g2 = "/^19[896512]\d{8}$/";
        $g3 = "/^166\d{8}$/";
        if (preg_match($g, $phone_number)) {
            return true;
        } else if (preg_match($g2, $phone_number)) {
            return true;
        } else if (preg_match($g3, $phone_number)) {
            return true;
        }

        return false;

    }

}