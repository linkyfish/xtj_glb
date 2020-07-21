<?php

namespace Reg;

use Ctrl\Controller;

Class Index extends Controller
{

    public function Index()
    {
        $code = $this->request->param('code');
        $code = trim($code);
        $disabled = 'disabled';
        if (empty($code)|| $code=='-1') {
            $disabled = '';
            $code='';
        }
        $url = 'https://'.$this->request->_S('HOST').'/?code='.$code;
        $ua = strtolower($this->request->_S('USER-AGENT'));
        if (strpos($ua, 'micromessenger') !== false) {//weixin
            return $this->view(get_defined_vars(),'Index.Jump');
        } elseif (strpos($ua, 'qqtheme') !== false) {
            return $this->view(get_defined_vars(),'Index.Jump');
        }

        $game_down_url = _CONF('game_down_url');
        $apk_url = _CONF('apk_down_url');
        $ios_url = _CONF('ios_down_url');
        return $this->View(get_defined_vars());
    }

    public function Index_Doregister()
    {
        $inputData = $this->request->post;
        if (!isset($inputData['userName'])) {
            $this->response('0001', [], "请输入手机号码");
        } else if (!$this->IsMobile($inputData['userName'])) {
            $this->response('0001', [], "手机号码格式错误");
        } else if (!isset($inputData['password'])) {
            $this->response('0001', [], "请输入密码");
        } else if (!isset($inputData['v_code'])) {
            $this->response('0001', [], "验证码不能为空");
        } else if (!isset($inputData['parentId'])) {
            $inputData['code'] = '000000';
        }

        $userExist = $this->Account->read(['account' => trim($inputData['userName'])]);
        if ($userExist) {
            $this->response('0001', [], "用户已存在");
        }
        $adRes = $this->User->read(['code'=>$inputData['parentId']]);
        if (!isset($adRes['id'])) {
            $this->response('0001', [], "推广号不存在");
        }
        if(!in_array($adRes['RoleID'],[9,111]))
        {
            $this->response('0001', [], "推广号不存在");
        }


        $smsRes = $this->SmsCode->count(['phone' => $inputData['userName'], 'Status' => 0, 'code' => $inputData['v_code']]);
        if ($smsRes < 1) {
            $this->response('0001', [], "验证码不正确");
        }

        $ip = $this->request->get_client_ip();
        $ipRegisterCount  = $this->Usr->count(['regip'=>$ip]);
        if($ipRegisterCount>=10)
        {
            $this->response('0001', [], "同IP注册超限制");
        }
        $tempRand = rand(1, 999999);
        $data['account'] = trim($inputData['userName']);
        $data['phone'] = trim($inputData['userName']);
        $data['times'] = date('Y-m-d H:i:s');
        $data['password'] = md5($tempRand . $inputData['password'] . date("Y-m-d"));
        $data['checktoken'] = $tempRand . '/' . $data['times'];
        $data['ip'] = $ip;//$_SERVER["REMOTE_ADDR"];
        $data['merchant_code'] = $inputData['parentId'];
        $data['pwd'] = $inputData['password'];
        $id = $this->Account->insert($data);
        if ($id > 0) {
            $this->SmsCode->update(['id'=>$smsRes['id']],['Status'=>1]);
            $this->response('0000', [], '注册成功');
        } else {
            $this->response('0001', [], '注册失败');
        }

    }

    public function  createTestAccount()
    {

        $tempRand = rand(1, 9999999999);
        $num = 1000;
        $result = '';
        for ($i = 0 ;$i <  $num;$i++ )
        {
            //$data['account'] = 13900000059+$i;

            $data['account'] = 'yczh'.$i;
            $data['times'] = date('Y-m-d H:i:s');
            $data['password'] = md5($tempRand . '123456' . date("Y-m-d"));
            $data['checktoken'] = $tempRand . '/' . $data['times'];
            $data['ip'] = $this->request->get_client_ip();
            $data['merchant_code'] = '696969';
            // $id = $this->Account->insert($data);
            //$result.=$data['account'].'添加成功';
        }
        return $result;
    }

    public function GetMsgOld()
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
        //$content = sprintf("【大洋娱港】您正在进行手机验证，验证码%s，有效期15分钟。", $code);
        $content = sprintf("【众博3D】您正在进行手机验证，验证码%s，有效期15分钟。", $code);
        list(, , $host, $remote_server) = explode('/', $sms_url);
        list($host, $port) = explode(':', $host);
        $data = [
            'account'=>$un,
            'password'=>$pw,
            'phone'=>$phone,
            'report'=>'true',
            'msg'=>$content,
        ];
        $json = json_encode($data);
        $cli = new \Swoole\Coroutine\Http\Client($host, 80);
        try{
            $cli->setHeaders([
                'Host' => $host,
                "User-Agent" => 'Chrome/49.0.2587.3',
                'Accept' => 'text/html,application/xhtml+xml,application/xml,application/json',
                'Accept-Encoding' => 'gzip',
            ]);

            $cli->post('/msg/send/json',$json);
            $cli->close();
            $response =  $cli->body;
            $obj = json_decode($response);
            if($obj->code ==0)
            {
                return json_encode(['code'=>0,'success'=>true,'msg'=>'短信发送成功']);
            }
            else{
                return json_encode(['code'=>0,'success'=>false,'msg'=>'短信发送失败']);
            }

        }
        catch (\Exception $exception)
        {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'短信发送失败']);
        }
    }

    public function Index_GetMsg()
    {

        $phone = $this->request->param('phone','');
        if(empty($phone))
        {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'手机号码不能为空']);
        }
        if (!isset($phone)) {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'请输入手机号码']);
        } else if (!$this->IsMobile($phone)) {

            return json_encode(['code'=>0,'success'=>false,'msg'=>'手机号码格式错误']);
        }

        $phone = $this->request->param('phone','');
        if(empty($phone))
        {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'手机号码不能为空']);
        }
        $phone = str_replace('+86','',$phone);

        $key = 'sms_'.$phone;
        $r = $this->Usr->CacheGet($key);
        if(!empty($r['time'])){
            return json_encode(['code'=>0,'success'=>false,'msg'=>'发送频繁，间隔2分钟']);
        }
        $this->Usr->CacheSet($key,['time'=>time()],120);

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

    public function userIpArea()
    {

        $ipList = $this->Logintable->select( [],  [],  'IP,ipregion,count(id) as playerCount',  1,5000000,  '',  'group by IP');
        $area = [
            '北京','天津',
            '上海', '重庆',
            '河北', '河南',
            '云南', '辽宁',
            '黑龙江', '湖南',
            '安徽', '山东',
            '新疆', '江苏',
            '浙江', '江西',
            '湖北' ,'广西',
            '甘肃', '山西',
            '内蒙古', '陕西',
            '吉林', '福建',
            '贵州', '广东',
            '青海', '西藏',
            '四川', '宁夏',
            '海南', '台湾',
            '香港', '澳门'

        ];

        $num = count($area);
        for($i=0 ; $i < $num ; $i++)
        {
            $data[$i] = 0;
            foreach ($ipList as $item)
            {

                if($item['ipregion'] == $area[$i] )
                {
                    $data[$i] += $item['playerCount'];
                }
            }
        }
        return $this->View(get_defined_vars());
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

    public function Index_GetIp()
    {
        $ip = $this->request->get_client_ip();
        return $ip;
    }
}