<?php

namespace Server;

/**
 * 签名助手 2017/11/19
 *
 * Class SignatureHelper
 */
class SendSms {


    public static function querySendDetails ($PhoneNumber , $SendDate , $CurrentPage = 1 , $PageSize = 10 , $OutId = '') {
        $params = array ();
        // *** 需用户填写部分 ***
        // fixme 必填: 请参阅 https://ak-console.aliyun.com/ 取得您的AK信息

        // fixme 必填: 短信接收号码
        $params["PhoneNumber"] = $PhoneNumber;

        // fixme 必填: 短信发送日期，格式Ymd，支持近30天记录查询
        $params["SendDate"] = $SendDate;

        // fixme 必填: 分页大小
        $params["PageSize"] = $PageSize;

        // fixme 必填: 当前页码
        $params["CurrentPage"] = $CurrentPage;

        // fixme 可选: 设置发送短信流水号
        !empty( $OutId ) AND $params["BizId"] = $OutId;

        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***

        // 初始化SignatureHelper实例用于设置参数，签名以及发送请求

        // 此处可能会抛出异常，注意catch
        $content = self::request( "dysmsapi.aliyuncs.com" , array_merge( $params , array (
                "RegionId" => "cn-hangzhou" ,
                "Action" => "QuerySendDetails" ,
                "Version" => "2017-05-25" ,
            ) ) );

        return $content;
    }

    public static function sendSms ($PhoneNumbers , $TemplateCode , $data = [] , $OutId = '') {

        $params = array ();


        // fixme 必填: 短信接收号码
        $params["PhoneNumbers"] = $PhoneNumbers;

        // fixme 必填: 短信签名，应严格按"签名名称"填写，请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/sign
        $params["SignName"] = $_ENV['conf']['dySignName'];

        // fixme 必填: 短信模板Code，应严格按"模板CODE"填写, 请参考: https://dysms.console.aliyun.com/dysms.htm#/develop/template
        $params["TemplateCode"] = $TemplateCode;

        // fixme 可选: 设置模板参数, 假如模板中存在变量需要替换则为必填项
        $params['TemplateParam'] = $data;

        // fixme 可选: 设置发送短信流水号
        !empty( $OutId ) AND $params['OutId'] = $OutId;

        // fixme 可选: 上行短信扩展码, 扩展码字段控制在7位或以下，无特殊需求用户请忽略此字段
        //$params['SmsUpExtendCode'] = "1234567";


        // *** 需用户填写部分结束, 以下代码若无必要无需更改 ***
        if ( !empty( $params["TemplateParam"] ) && is_array( $params["TemplateParam"] ) ) {
            $params["TemplateParam"] = json_encode( $params["TemplateParam"] , JSON_UNESCAPED_UNICODE );
        }

        // 此处可能会抛出异常，注意catch
        $content = self::request( "dysmsapi.aliyuncs.com" , array_merge( $params , array (
                "RegionId" => "cn-hangzhou" ,
                "Action" => "SendSms" ,
                "Version" => "2017-05-25" ,
            ) ) );
        return $content;
    }

    /**
     * 生成签名并发起请求
     *
     * @param $_ENV ['conf']['dyaccessKeyId'] string AccessKeyId (https://ak-console.aliyun.com/)
     * @param $_ENV ['conf']['accessKeySecret'] string AccessKeySecret
     * @param $domain string API接口所在域名
     * @param $params array API具体参数
     * @param $security boolean 使用https
     * @return bool|\stdClass 返回API接口调用结果，当发生错误时返回false
     */
    public static function request ($domain , $params , $security = false) {
        $apiParams = array_merge( array (
            "SignatureMethod" => "HMAC-SHA1" ,
            "SignatureNonce" => uniqid( mt_rand( 0 , 0xffff ) , true ) ,
            "SignatureVersion" => "1.0" ,
            "AccessKeyId" => $_ENV['conf']['dyaccessKeyId'] ,
            "Timestamp" => gmdate( "Y-m-d\TH:i:s\Z" ) ,
            "Format" => "JSON" ,
        ) , $params );
        ksort( $apiParams );

        $sortedQueryStringTmp = "";
        foreach ($apiParams as $key => $value) {
            $sortedQueryStringTmp .= "&" . self::encode( $key ) . "=" . self::encode( $value );
        }

        $stringToSign = "GET&%2F&" . self::encode( substr( $sortedQueryStringTmp , 1 ) );

        $sign = base64_encode( hash_hmac( "sha1" , $stringToSign , $_ENV['conf']['dyaccessKeySecret'] . "&" , true ) );

        $signature = self::encode( $sign );

        $url = ($security ? 'https' : 'http') . "://{$domain}/?Signature={$signature}{$sortedQueryStringTmp}";
        try {
            $content = self::fetchContent( $url );
            return json_decode( $content , 1 );
        } catch(\Exception $e) {
            return false;
        }
    }

    private static function encode ($str) {
        $res = urlencode( $str );
        $res = preg_replace( "/\+/" , "%20" , $res );
        $res = preg_replace( "/\*/" , "%2A" , $res );
        $res = preg_replace( "/%7E/" , "~" , $res );
        return $res;
    }

    private static function fetchContent ($url) {
        $ch = curl_init();
        curl_setopt( $ch , CURLOPT_URL , $url );
        curl_setopt( $ch , CURLOPT_TIMEOUT , 5 );
        curl_setopt( $ch , CURLOPT_RETURNTRANSFER , 1 );
        curl_setopt( $ch , CURLOPT_HTTPHEADER , array ("x-sdk-client" => "php/2.0.0") );
        curl_setopt( $ch , CURLOPT_IPRESOLVE , CURL_IPRESOLVE_V4 );
        if ( substr( $url , 0 , 5 ) == 'https' ) {
            curl_setopt( $ch , CURLOPT_SSL_VERIFYPEER , false );
            curl_setopt( $ch , CURLOPT_SSL_VERIFYHOST , false );
        }
        $rtn = curl_exec( $ch );
        if ( $rtn === false ) {
            trigger_error( "[CURL_" . curl_errno( $ch ) . "]: " . curl_error( $ch ) , E_USER_ERROR );
        }
        curl_close( $ch );
        return $rtn;
    }
}