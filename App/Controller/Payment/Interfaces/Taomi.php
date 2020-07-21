<?php




namespace Payment\Interfaces;

class   Taomi extends Order
{


    public function create($usrid, $money, $Type, $config)
    {
        $order = parent::create($usrid, $money, $Type, $config); // TODO: Change the autogenerated stub
        $this->AppUrl = $config['AppUrl'];
        $PayType = 'ALI_CODE';
        switch ($Type) {
            case 20:
                $PayType = 16;
                break;
            default:
                $this->app->response('0001', [], '支付通道暂时关闭');

        }
        /*
        $formData = [
            'pay_type'=>$PayType,
            'amount' => bcmul($order['Money'],0.01,2),
            'attach'=>'666',
            'client_ip'=>$this->app->request->get_client_ip(),
            'ref_number' =>$order['OrderNo'],
            'notify_url'=>$this->NotifyUrl,
            'merchant_code'=>$config['AppID'],
            'timestamp'=>time(),
            'nonce'=>$this->NonceStr(16)
        ];
*/
        $params  = [
            'pay_type'        => 16,//交易回调通知地址
            'amount' => bcmul($order['Money'],0.01,2),
            'attach'          => 'Xepay',
            'client_ip'=>$this->app->request->get_client_ip(),
            'ref_number'      => $order['OrderNo'],
            'notify_url'      => $this->NotifyUrl,
        ];
        $header = [
            'merchant_code'   => $config['AppID'],
            'timestamp'       => time(),
            'nonce'           => $this->NonceStr(16),
        ];

        $data = array_merge($params,$header);
        // return json_encode($data);
        $sign = $this->getSign($data,$config['AppKey']);
        xn_log($this->AppUrl . '/order/create' . ' ' . xn_json_encode($data) . ' ', 'payapi');
        $result = $this->httpPost($params, $sign, $header['timestamp'], $header['nonce'],$config['AppID']);
        $result = json_decode($result,true);
        if($result['code'] == 2000){//状态【1代表正常】【0代表错误】
            if(array_key_exists('url', $result['data'])){
                $qrcode = $result['data']['url'];
                // header("Location:$qrcode");
                $this->app->response('0000', [], '下单成功', $qrcode, 302);
            }
        }else{
            $msg = '获取支付链接失败,' .json_encode($result); //如果转换错误，原样输出返回
            $this->app->response('0001', [], '下单失败'.$msg);
        }
    }

    public function Ratify($order, $data, $config)
    {
        $formData = [
            'merchant_user_id' => $config['AppID'],
            'out_trade_no' => $order['OrderNo'],
            "sign_type" => "MD5",
        ];

        $formData['sign'] = $this->makeSign($formData, $config['AppKey']);

        $data = $this->api('/gateway/merchant/query', json_encode($formData), ['Content-Type' => 'application/json']);
        if ($data['code'] == 200 && !empty($data['data']['trade_order_no'])) {
            if (($data['data']['order_status'] == 'SUCCESS' || $data['data']['order_status'] == 'success') && !empty($data['data']['pay_amount'])) {
                $order['RealMoney'] = $data['data']['pay_amount'] * 100;
                parent::Notify($order, $data, $config); // TODO: Change the autogenerated stub
                $this->app->response('0000', [], '订单支付成功');
            }
            $this->app->response('0001', [], '订单尚未支付');
        } else {
            $this->app->response('0001', [], '订单查询失败');
        }
    }

    public function Notify($order, $inputData, $config)
    {
        //{"msg":"\u64cd\u4f5c\u6210\u529f","amount":"10000","code":"MSG_OK","transactiondate":"2020-03-02 17:47:05","mer_id":"15234","sign":"E4025D935BCC2C023E3477BB1DFEEE93","remark":"\u8ba2\u5355\u6210\u529f","transactiontype":"\u4ee3\u6536","result":"success","real_amount":"9800.00","businessnumber":"No5020200302174705e3fa88a6","inputdate":"2020-03-02 17:47:05","sign_type":"md5","status":"\u6210\u529f","0":"payment","1":"order","2":"notify","3":"10_465","rawContent":"msg=%E6%93%8D%E4%BD%9C%E6%88%90%E5%8A%9F&amount=10000&code=MSG_OK&transactiondate=2020-03-02+17%3A47%3A05&mer_id=15234&sign=E4025D935BCC2C023E3477BB1DFEEE93&remark=%E8%AE%A2%E5%8D%95%E6%88%90%E5%8A%9F&transactiontype=%E4%BB%A3%E6%94%B6&result=success&real_amount=9800.00&businessnumber=No5020200302174705e3fa88a6&inputdate=2020-03-02+17%3A47%3A05&sign_type=md5&status=%E6%88%90%E5%8A%9F"}
        if ($order['Status'] == 1) {
            return 'success';
        }

        $data = json_decode($inputData['rawContent'],true);
        $sign = $data['sign'];
        $_data = [
            "ref_number" => $data['ref_number'],
            "amount" => bcmul($data['amount']*100,0.01,2),
            "pay_type" => $data['pay_type'],
            "attach" => $data['attach'],
            "merchant_code" => $config['AppID'],
        ];


        if ($sign != $this->paramArraySign($_data, $config['AppKey'])) {
            xn_log('签名错' . json_encode($data), 'sign_erro');
            return 'error';
        }
        if ($order['Status'] == 0) {
                $order['RealMoney'] = $data['amount']*100;
                parent::Notify($order, $data, $config); // TODO: Change the autogenerated stub
                return 'success';
        }

        return 'error';

    }

    public function makeSign($paramArr, $appSecret)
    {
        //sign = 大写(md5(key1=value1& key2=value2&key3=value4&…key7=value7&密钥))
        unset($paramArr['sign_type']);
        unset($paramArr['sign']);
        $sign = '';
        ksort($paramArr);
        foreach ($paramArr as $key => $val) {
            $sign .= '&' . $key . '=' . $val;

        }
        $sign .= '&' . $appSecret;
        $sign = substr($sign, 1);
        $sign = strtoupper(md5($sign));

        return $sign;
    }

    protected function NonceStr($length = 16)
    {
        $returnStr = '';
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        for ($i = 0; $i < $length; $i++) {
            $returnStr .= $pattern{mt_rand(0, 61)};
        }
        return $returnStr;
    }

    protected function getSign($arr, $key)
    {
        /* $hashstr  = "amount=" . $arr['amount'] . "&attach="
               . $arr['attach'] . "&client_ip=" . $arr['client_ip']
               . "&merchant_code=" . $this->merno . "&nonce=" .$arr['nonce']
               . "&notify_url=" . urlencode($arr['notify_url']) . "&pay_type=" . $arr['pay_type']
               . "&ref_number=" . $arr['ref_number'] . "&timestamp=" . $arr['timestamp'];  */
        ksort($arr);
        $hashstr = http_build_query($arr);
        return hash_hmac('sha256', $hashstr, $key);
    }

    private function httpPost($params, $sign, $timestamp, $nonce, $appID)
    {
        $headers = array();
        $headers[] = "Content-Type:application/json";
        $headers[] = "sign:$sign";
        $headers[] = "timestamp:$timestamp";
        $headers[] = 'nonce:' . $nonce;
        $headers[] = "merchant_code:" . $appID;
        $params['amount'] = (float)$params['amount'];
        $data_json = json_encode($params);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://api.mgtdpay.site/order/create');
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data_json);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        $response = curl_exec($ch);
        curl_close($ch);
        return $response;
    }

    private function paramArraySign($param,$key)
    {
        unset($param['sign']);
        ksort($param);
        $hashstr = http_build_query($param);
        return hash_hmac('sha256', $hashstr, $key);
    }

    private function getIP()
    {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $realip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                $realip = $_SERVER['HTTP_CLIENT_IP'];
            } else {
                $realip = $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                $realip = getenv("HTTP_X_FORWARDED_FOR");
            } elseif (getenv("HTTP_CLIENT_IP")) {
                $realip = getenv("HTTP_CLIENT_IP");
            } else {
                $realip = getenv("REMOTE_ADDR");
            }
        }
        return $realip;
    }


}