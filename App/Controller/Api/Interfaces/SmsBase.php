<?php


namespace Api\Interfaces;


class SmsBase
{

    public function  rendSmsTpl($tplName,$data)
    {
        $content = $this->getSmsTplContent($tplName);
        $content = $this->replaceVar($content,$data);
        return $content;
    }
    public function  getSmsTplContent($tpl)
    {
        $content = '';
        switch ($tpl)
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
                $content = "【淘金】尊敬的客户您好,您的正在修改信息验证码为【{code}】";
                break;
        }
        return $content;

    }
    public function replaceVar($str,$data)
    {
        foreach ($data as $key=>$item)
        {
          $str = str_replace('{'.$key.'}',$item,$str);

        }
        return $str;
    }
}