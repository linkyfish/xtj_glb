<?php


namespace Api\Interfaces;


class DwcSms extends SmsBase
{

   protected $smsName = '第二个临时短信';
   public $appcConfig = [
    ];
    public function __construct($app)
    {
        $this->appcConfig ['host'] = 'dx.010dx.com';
        $this->appcConfig ['account'] = '1699dwc' ;
        $this->appcConfig ['password'] = '1699Dw1699';
        $this->appcConfig ['userid'] = '24876';

    }

    public function  send($param,$phone,$tpl)
    {

        
        $tplContent = $this->getSmsTplContent($tpl);
       // $content = $this->replaceVar($tplContent,$data['vars']);
        $content = parent::rendSmsTpl($tpl,$param['vars']);
        //$content = urlencode($content);
        $content = iconv("utf-8","gb2312//IGNORE",$content);
        $url = "?username={$this->appcConfig['username']}&password={$this->appcConfig['password']}&message={$content}&phone={$phone}&epid={$this->appcConfig['epid']}&linkid=1234567890&subcode=";

        $cli = new \Swoole\Coroutine\Http\Client($this->appcConfig['host'], 8061);
        $cli->setHeaders([
            'Host' => $this->appcConfig['host'],
            'Accept-Encoding' => 'gzip',
            'Accept' => 'text/html,application/xhtml+xml,application/xml,application/json',
        ]);

        $cli->set(['timeout' => 60]);
        try{
            $cli->get('/'.$url);
            $response = $cli->body;
            $cli->close();
            if($response==='00')
            {
                return ['code'=>0,'success'=>true,'msg'=>'发送成功'];
            }
            return ['code'=>0,'success'=>false,'msg'=>$response];

        }
        catch (\Exception $ex)
        {
            xn_log( ' 短信发送异常 '. $this->smsName.' 返回 ' .$cli->body, 'api_error');
            return ['code'=>0,'success'=>false,'msg'=>$response];
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
                $content ="【淘金】尊敬的客户您好,您的账号已免费获取{code}积分,请登录平台及时领取。";
                break;
            case  'xiafen':
                $content = "【淘金】尊敬的客户您好,您的账号已下分成功";
                break;
            case  'xiugaiyinhang':
                $content = "【淘金】尊敬的客户您好,您的正在修改信息验证码为{code}";
                break;
        }
        return $content;

    }


}
