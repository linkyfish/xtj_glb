<?php

namespace Payment\Interfaces;

class Chanbaobao extends Order
{


    public function create($usrid, $money, $Type, $config)
    {
        $order = parent::create($usrid, $money, $Type, $config); // TODO: Change the autogenerated stub
        $this->AppUrl = $config['AppUrl'];
        $PayType = 1;
        //empty($config['AppSess']) && $config['AppSess']='alipay_sm';

        switch ($Type) {
            case 20:
                $PayType =2;//alipay
                break;
            case 10:
                $PayType =1;//weixin
                break;
            case 50:
                $PayType =3;//云闪付
                break;

            default:
                $this->app->response('0001', [], '支付通道暂时关闭');

        }

        $formData = [
            'amount' => bcmul($order['Money'],0.01,2),
            'mch_id' => $config['AppID'],
            'out_trade_no' => $order['OrderNo'],
            'success_url' => $this->ReturnUrl,
            'notify_url' => $this->NotifyUrl,
            "type"=>$PayType,
             "ip"=>$this->app->request->get_client_ip(),

        ];



        $formData['sign'] = $this->makeSign($formData,$config['AppKey']);

        $data = $this->api('/order/service_order', $formData, ['Content-Type' => 'application/x-www-form-urlencoded']);

        if ($data['code'] == 200 && !empty($data['pay_url'])) {
            //$this->app->Order->update(['ID' => $order['ID']], ['PayOrderNo' => $data['data']['trade_order_no']]);
            $this->app->response('0000', [], '下单成功', $data['pay_url'], 302);
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
            return 'success';
        }
        $sign = $data['sign'];
        unset($data['sign']);
        $_data = [
            'amount' =>$data['amount'],
            'status' => $data['status'],
            'pay_money' => $data['pay_money'],
            'mch_id' => $data['mch_id'],
            'out_trade_no' => $data['out_trade_no'],
            'order_no' => $data['order_no'],
            'charge_type' => $data['charge_type'],
        ];


        if ($sign != $this->makeSign($_data, $config['AppKey'])) {
            return 'error';
        }


        if ($order['Status'] == 0) {
            if ($data['status'] == 1) {
                $order['RealMoney'] = $data['amount']*100;
                parent::Notify($order, $data, $config); // TODO: Change the autogenerated stub
                return 'SUCCESS';
            } else {
                return 'error';
            }
        }

        return 'error';

    }

    public function makeSign($paramArr, $appSecret)
    {
        unset($paramArr['sign_type']);
        unset($paramArr['sign']);
        $sign = '';
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $sign .= '&' . $key . '=' . $val;
            }
        }
        $sign .= '&key='.$appSecret;
        $sign = substr($sign, 1);
        $sign = md5($sign);
        return $sign;
    }

}