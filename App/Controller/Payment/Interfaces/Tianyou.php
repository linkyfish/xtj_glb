<?php

namespace Payment\Interfaces;

class Tianyou extends Order
{


    public function create($usrid, $money, $Type, $config)
    {
        $order = parent::create($usrid, $money, $Type, $config); // TODO: Change the autogenerated stub
        $this->AppUrl = $config['AppUrl'];
        $PayType = 'alipay_sm';
        //empty($config['AppSess']) && $config['AppSess']='alipay_sm';

        switch ($Type) {
            case 20:
                $PayType = '129';
                break;
            default:
                $this->app->response('0001', [], '支付通道暂时关闭');

        }

        $formData = [
            'appid' => $config['AppID'],
            'amount' => bcmul($order['Money'],0.01,2),
            'success_url' => $this->ReturnUrl,
            'callback_url' => $this->NotifyUrl,
            'error_url' => $this->ReturnUrl,
            'out_uid' => $usrid,
            'out_trade_no' => $order['OrderNo'],
            'version' => 'v1.1',
            'return_type'=>'mobile'
        ];

        ksort($formData);
        //$content = sprintf("Amount=%s&MerId=%s&MerOrderNo=%s&MerOrderTime=%s&NotifyUrl=%s&PayType=%s&Version=3&key=%s", $order['Money'], $config['AppID'], $order['OrderNo'], $time, $this->NotifyUrl, $PayType, $config['AppKey']);
        $content = http_build_query($formData);
        $formData['sign'] = $this->makeSign($formData,$config['AppKey']);
        $data = $this->api('/sh-alipay-gateway/gateway/api/unifiedorder.do?format=json', $formData, ['Content-Type' => 'application/x-www-form-urlencoded']);
        if ($data['code'] == '200') {
            $this->app->Order->update(['ID' => $order['ID']], ['PayOrderNo' => $data['data']['out_trade_no']]);
            $this->app->response('0000', [], '下单成功', urldecode($data['data']['qrcode']), 302);
        } else {
            $this->app->response('0001', [], '下单失败');
        }

    }


    public function Notify($order, $data, $config)
    {
        if ($order['Status'] == 1) {
            return 'success';
        }
        $sign = $data['sign'];
        unset($data['sign']);
        $_data = [
            'amount' => $data['amount'],
            'appid' => $data['appid'],
            'success_url' => $data['success_url'],
            'callback_url' =>$this->NotifyUrl,
            'error_url' => $data['error_url'],
            'out_uid' =>$data['out_uid'],
            'out_trade_no' => $data['out_trade_no'],
            'version' => 'v1.1',
             'return_type'=>'mobile'

        ];

       // xn_log(  '输入:'.xn_json_encode($data).'\n'.'修正'.xn_json_encode($_data), 'payapi_notice_error');

       //return $sign.'-'.$this->makeSign($_data, $config['AppKey']);
        /*
        if ($sign != $this->makeSign($_data, $config['AppKey'])) {
           // return '签名不对 ';
            xn_log('签名不对'.$sign, 'payapi_error');
            return 'error';
        }
        */
       // return json_encode($order);
        if ($order['Status'] == 0) {
            if ($data['callbacks'] == 'CODE_SUCCESS') {
                $order['RealMoney'] = $data['amount']*100;
                parent::Notify($order, $data, $config); // TODO: Change the autogenerated stub
                return 'success';
            } else {
                return 'error';
            }
        }

        return 'error';

    }

    public function makeSign($paramArr, $appSecret)
    {
        $sign = '';
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $sign .= '&' . $key . '=' . $val;
            }
        }
        $sign .= '&key=' . $appSecret;
        $sign = substr($sign, 1);
        $sign = strtoupper(md5($sign));
        return $sign;
    }

}