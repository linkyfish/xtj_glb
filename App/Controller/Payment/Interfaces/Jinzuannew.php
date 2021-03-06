<?php

namespace Payment\Interfaces;

class Jinzuannew extends Order
{


    public function create($usrid, $money, $Type, $config)
    {
        $order = parent::create($usrid, $money, $Type, $config); // TODO: Change the autogenerated stub
        $this->AppUrl = $config['AppUrl'];
        $PayType = 'alipay_operation';
        switch ($Type) {
            case 20:
                $PayType ='1';
                break;

            default:
                $this->app->response('0001', [], '支付通道暂时关闭');

        }
        $formData = [

            'time'=>time(),
            'mch_id'=> $config['AppID'],
            'ptype'=>$PayType,
            'order_sn'=> $order['OrderNo'],
            'money'=>bcmul($order['Money'],0.01,2),
            'goods_desc'=>'buy',
            'client_ip'=>$this->app->request->get_client_ip(),
            'format'=>'page',
            'notify_url'=>$this->NotifyUrl
        ];

        $formData['sign'] = $this->makeSign($formData,$config['AppKey']);
        $url = $this->AppUrl.'/?c=Pay&'.http_build_query($formData);
        $this->app->response('0000', [], '下单成功',$url, 302);
        /*
        $data = $this->api('/?c=Pay&', $formData,['Content-Type' => 'application/x-www-form-urlencoded']);
        if ($data['code'] == 1 && !empty($data['data']['qrcode'])) {
            $this->app->Order->update(['ID' => $order['ID']], ['PayOrderNo' => $data['data']['order_sn']]);
            $this->app->response('0000', [], '下单成功', $data['data']['qrcode'], 302);
        } else {
            $this->app->response('0001', [], '下单失败');
        }
        */
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
            'pt_order'=>$data['pt_order'],
            'sh_order'=>$data['sh_order'],
            'money'=>$data['money'],
            'status'=>$data['status'],
            'time'=>$data['time']
        ];

        if ($sign != $this->makeSign($_data, $config['AppKey'])) {

            return 'fail';
        }
        if ($order['Status'] == 0) {
            if ($data['status'] == 'success') {
                $order['RealMoney'] = $data['money']*100;
                parent::Notify($order, $data, $config); // TODO: Change the autogenerated stub
                return 'success';
            } else {
                return 'fail';
            }
        }

        return 'fail';

    }

    public function makeSign($paramArr, $appSecret)
    {
        unset($paramArr['sign']);
        $sign = '';
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            if ($key != '' && $val != '') {
                $sign .= '&' . $key . '=' . $val;
            }
        }
        $sign = trim($sign,'&');
        $sign = md5($sign . "&key=" . $appSecret);
        return $sign;
    }

}