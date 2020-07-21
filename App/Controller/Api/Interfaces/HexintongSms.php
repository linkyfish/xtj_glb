<?php


namespace Api\Interfaces;


class HexintongSms extends SmsBase
{

   protected $smsName = 'hexintong';
    public $appcConfig = [
    ];
    public function __construct($app)
    {
        $this->appcConfig ['host'] = 'dx.010dx.com';
        $this->appcConfig ['username'] = '1699dwc' ;
        $this->appcConfig ['password'] = '1699Dw1699';
        $this->appcConfig ['epid'] = '1699dwc';

    }

    public function  send($param,$phone,$tpl)
    {


        $tplContent = $this->getSmsTplContent($tpl);
       // $content = $this->replaceVar($tplContent,$data['vars']);
        $content = parent::rendSmsTpl($tpl,$param['vars']);
        $post_data['userid'] = 24876;
        $post_data['account'] =  $this->appcConfig ['username'];
        $post_data['password'] = $this->appcConfig ['password'] ;
        $post_data['content'] = $content; //短信内容需要用urlencode编码下
        $post_data['mobile'] = $phone;
        //$post_data['sendtime'] =0; ; //date('YmdHis'); //不定时发送，值为0，定时发送，输入格式YYYYMMDDHHmmss的日期值
        $url = 'sms.aspx?action=send';
        $cli = new \Swoole\Coroutine\Http\Client($this->appcConfig['host'], 8888);
        $cli->setHeaders([
            'Host' => $this->appcConfig['host'],
            'Accept-Encoding' => 'gzip',
            'Content-Type' => 'application/x-www-form-urlencoded',
            'Accept' => 'text/html,application/xhtml+xml,application/xml,application/json',
        ]);

        $cli->set(['timeout' => 60]);
        try{
            $cli->post('/'.$url, $post_data);
            $response = $cli->body;
            $obj = simplexml_load_string($cli->body);
            $cli->close();
            if($obj->returnstatus=='Success')
            {
                return ['code'=>0,'success'=>true,'msg'=>'发送成功','result'=>$cli->body];
            }
            return ['code'=>0,'success'=>false,'msg'=>'发送失败','result'=>$cli->body];

        }
        catch (\Exception $ex)
        {
            xn_log( ' 短信发送异常 '. $this->smsName.' 返回 ' .$cli->body, 'api_error');
            return ['code'=>0,'success'=>false,'msg'=>$response,'result'=>$cli->body];
        }
    }

    public function  getSmsTplContent($normal)
    {
        $content = '';
        switch ($normal)
        {
            case  'normal':
                $content = "【淘金】您正在进行手机验证，验证码{code}，有效期15分钟。";
                break;
            case  'smsSong':
                $content ="";
                break;
            case  'xiafen':
                $content = "";
                break;
            case  'xiugaiyinhang':
                $content = "";
                break;
        }
        return $content;

    }


}
