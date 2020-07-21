<?php

namespace Payment\Interfaces;

class Jiudingyifu extends Order
{


    public function create($usrid, $money, $Type, $config)
    {
        $order = parent::create($usrid, $money, $Type, $config); // TODO: Change the autogenerated stub
        $this->AppUrl = $config['AppUrl'];
        $PayType = 'alipay';
        switch ($Type) {
            case 20:
                $PayType ='alipay';
                break;
            default:
                $this->app->response('0001', [], '支付通道暂时关闭');
        }
        $formData = [
            'amount' => bcmul($order['Money'],0.01,2),
            'payType'=>$PayType,
            'merchantNum' => $config['AppID'],
            'orderNo' => $order['OrderNo'],
            'notifyUrl' => $this->NotifyUrl,
            'returnUrl' => $this->ReturnUrl,
            'attch' =>'商品订单01',
        ];

        $formData['sign'] = $this->makeSign($formData,$config);
        $data = $this->api('/api/startOrder', $formData,['Content-Type' => 'application/x-www-form-urlencoded']);
        if ($data['success'] == true && !empty($data['data']['payUrl'])) {
            $this->app->response('0000', [], '下单成功', $data['data']['payUrl'], 302);
        } else {
            $this->app->response('0001', [], '下单失败');
        }
    }

    public function Ratify($order, $data, $config){


        $formData = [
            'merchant_user_id' => $config['AppID'],
            'out_trade_no' => $order['OrderNo'],
            "sign_type"=>"MD5",
        ];

        $formData['sign'] = $this->makeSign($formData,$config['AppKey']);
        $data = $this->api('/gateway/merchant/query', json_encode($formData), ['Content-Type' => 'application/json']);
        if ($data['code'] == 200 && !empty($data['data']['trade_order_no'])) {
            if(($data['data']['order_status']=='SUCCESS'||$data['data']['order_status']=='success') &&!empty($data['data']['pay_amount'])){
                $order['RealMoney'] = $data['data']['pay_amount']*100;
                parent::Notify($order, $data, $config); // TODO: Change the autogenerated stub
                $this->app->response('0000', [], '订单支付成功');
            }
            $this->app->response('0001', [], '订单尚未支付');
        } else {
            $this->app->response('0001', [], '订单查询失败');
        }
    }

    public function Notify($order, $data, $config)
    {
        if ($order['Status'] == 1) {
            return 'SUCCESS';
        }
        $sign = $data['sign'];
        unset($data['sign']);
        $_data = [
            'merchantNum' => $data['merchantNum'],
            'orderNo'=> $data['orderNo'],
            'platformOrderNo' => $data['platformOrderNo'],
            'amount' => $data['amount'],
            'attch' => $data['attch'],
            'state' => $data['state'],
            'payTime' =>$data['payTime'],
            'actualPayAmount' =>$data['actualPayAmount'],
        ];

        if ($sign == $this->makeNotifySign($_data, $config)) {
            if ($order['Status'] == 0) {
                $order['RealMoney'] = $data['amount']*100;
                parent::Notify($order, $data, $config); // TODO: Change the autogenerated stub
                return 'success';
            }
        }



    }

    public function makeSign($data,$appConfig)
    {

        //	签名【md5(商户号+商户订单号+支付金额+异步通知地址+商户秘钥)】
        $sign = md5($appConfig['AppID'].$data['orderNo'].$data['amount'].$data['notifyUrl'].$appConfig['AppKey']);
        return $sign;
    }

    public function makeNotifySign($data,$appConfig)
    {

        //	签名【md5(商户号+商户订单号+支付金额+异步通知地址+商户秘钥)】
        $sign = md5($data['state'].$appConfig['AppID'].$data['orderNo'].$data['amount'].$appConfig['AppKey']);
        return $sign;
    }


}