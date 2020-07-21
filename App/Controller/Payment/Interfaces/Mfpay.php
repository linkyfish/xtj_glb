<?php

namespace Payment\Interfaces;

class Mfpay extends Order
{


    public function create($usrid, $money, $Type, $config)
    {
        $order = parent::create($usrid, $money, $Type, $config); // TODO: Change the autogenerated stub
        $this->AppUrl = $config['AppUrl'];
        $PayType = 'alipay_operation';
        //empty($config['AppSess']) && $config['AppSess']='alipay_sm';

        switch ($Type) {
//            case 10:
//                $PayType = 'wxpay_sm';
//                break;
            case 20:
                $PayType ='alipay_static_qrcode';
                break;
//            case 30:
//                $PayType = 'quick';
//                break;
//            case 50:
//                $PayType = 'uppay';
//                break;
            default:
                $this->app->response('0001', [], '支付通道暂时关闭');

        }

        $formData = [
            'route_id'=>1,
            'merchant_id'=>$config['AppID'],
            'money'=> bcmul($order['Money'],0.01,0)*100,
            'sn'=>$order['OrderNo'],
            'trade_time'=>date('YmdHis',time()),
            'body'=>'body',
            'request_time'=>date('Ym',time()),
            'version'=>'1.0.0',
            'notify_url'=>$this->NotifyUrl,
        ];

        $formData['key_sign'] = $this->makeSign($formData,$config['AppKey']);

        $data = $this->api('/papiservices/payadd',json_encode($formData), ['Content-Type' => 'application/json']);

        //$data = $this->api('/papiservices/payadd', $formData, ['Content-Type' => 'application/x-www-form-urlencoded']);
        if ($data['code'] == '10000') {
            $this->app->Order->update(['ID' => $order['ID']], ['PayOrderNo' => $data['data']['plat_sn']]);
            $this->app->response('0000', [], '下单成功', $data['data']['url'], 302);
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
        $data = $this->api('/papiservices/payfind', json_encode($formData), ['Content-Type' => 'application/json']);
        if ($data['code'] == 200 && !empty($data['data']['trade_order_no'])) {
            if(($data['data']['order_status']=='SUCCESS'||$data['data']['order_status']=='success') &&!empty($data['data']['pay_amount'])){
                $order['RealMoney'] = $data['data']['pay_amount']*100*0.01;
                parent::Notify($order, $data, $config); // TODO: Change the autogenerated stub
                $this->app->response('0000', [], '订单支付成功');
            }
            $this->app->response('0001', [], '订单尚未支付');
        } else {
            $this->app->response('0001', [], '订单查询失败');
        }
    }

    public function Notify($order, $dataInput, $config)
    {
       // $data = xn_json_decode($dataInput['rawContent']);
        $data =  json_decode($dataInput['rawContent'],true);

        if ($order['Status'] == 1) {
           return 'success';
        }
        $sign = $data['key_sign'];
        if ($sign != $this->makeSign($data, $config['AppKey'])) {
            return '签名错误'.$sign.'-'.$this->makeSign($data, $config['AppKey']);
        }

        if($data['result_code'] != 1){
            echo '支付失败';
        }
        if ($order['Status'] == 0) {
            if ($data['result_code'] ==1) {
                $order['RealMoney'] = $data['money'];
                parent::Notify($order, $data, $config); // TODO: Change the autogenerated stub
                return 'success';
            } else {
                return '支付失败';
            }
        }

        return '支付失败';

    }

    public function makeSign($data, $appSecret)
    {
        $str = "";

        ksort($data);

        foreach($data as $k=>$v){
            if($v === '' || $k=='key_sign'){
                continue;
            }
            $str .= $k."=".$v.'&';
        }

        $str = substr($str,0,strlen($str)-1);

        $sign =  strtolower(md5($str.$appSecret));

        return $sign;
    }

}