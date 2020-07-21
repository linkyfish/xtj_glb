<?php

namespace Payment;

use Ctrl\Controller;

Class Order extends Controller
{

    public function Index()
    {
        return xn_json_encode($this->request->server);
    }

    public function Created()
    {

        $paymentStatus = _CONF('PaymentPageOpen');
        if($paymentStatus ==0)
        {
           $this->response('0001',[],'功能正在建设中。。。。');
        }
        include_once _include(__APPDIR__ . 'Controller/Payment/Interfaces/Order.php');
        $usrid = $this->request->param('uid', 0);
        $defined = $this->request->param('defined', 0);
        $Type = $this->request->param('paytype', 0);
        empty($Type) && $Type = $this->request->param('payType', 0);
        $Type<10 && $Type*=10;

        $this->CheckEmpty([$usrid, $Type], ['游戏账户', '支付渠道']);
        //$defined < 1 AND $this->response('0001', [], '金额异常');
        !in_array($Type, [10, 20, 30, 40, 50]) AND $this->response('0001', [], '支付通道未开启');
        $usr = $this->Usr->read(['usrid'=>$usrid]);
        empty($usr['usrid']) && $this->response('0001',[],'用户不存在');

        $channelId = $this->request->param('channel', 0);
        $config = $this->PayConfig->read(['id' => $channelId]);
        $money_arr= explode(',',$config['Price'.$Type]);
        $money = $defined*100;
        /*
        if(is_array($defined,$money_arr))
        {
            $money=  $defined*100;
        }
        else{
            $money= 0;
        }
        */
        empty($money) AND $this->response('0001', [], '支付金额有误');//暂未限制

        $payment = ucfirst(strtolower($config['Channel']));

        if (is_file(__APPDIR__ . 'Controller/Payment/Interfaces/' . $payment . '.php')) {
            include_once _include(__APPDIR__ . 'Controller/Payment/Interfaces/' . $payment . '.php');
            $contor = '\\Payment\\Interfaces\\' . $payment;
            $pay = new $contor($this);
            return $pay->create($usrid, $money, $Type, $config);
        } else {
            $this->response('0001', [], '支付平台等待接入');
        }
    }

    public function Notify()
    {
        include_once _include(__APPDIR__ . 'Controller/Payment/Interfaces/Order.php');
        $data = $this->request->param();
        $data['rawContent']= $this->request->rawContent();
        xn_log($this->request->get_client_ip().' 回调时间  '.date('Y-m-d H:i:s',time()).' '.json_encode($data).'raw '.$this->request->rawContent(), 'pay_notify');
        //$data = '{"Amount":"100","AmountReal":"100","BalanceTime":"20191224010209","Fee":"3","MerId":"8178","MerOrderNo":"No20201912240101522fe29d69","MerOrderTime":"20191224010152","PayOrderNo":"19122400173259798","PayOrderTime":"20191224010153","PayStatus":"success","Sign":"970448D61847F30CF874CB0884C68EC9","0":"payment","1":"order","2":"notify","3":"2_42"}';
        //$data = xn_json_decode($data);

        list($payment_id,$order_id) =explode('_', $this->request->param('3',''));
        $config= $this->PayConfig->read(['id' => $payment_id]);
        $payment = ucfirst(strtolower($config['Channel']));
        if (is_file(__APPDIR__ . 'Controller/Payment/Interfaces/' . $payment . '.php')) {
            include_once _include(__APPDIR__ . 'Controller/Payment/Interfaces/' . $payment . '.php');
            $order = $this->Order->read(['ID'=>$order_id]);
            empty($order['ID']) AND $this->response('0001',[],'订单不存在');
            $this->OrderNotify->insert(['OrderID'=>$order['ID'],'CreateAT'=>time(),'Info'=>xn_json_encode($data),'IP'=>$this->request->get_client_ip(1)]);
            $contor = '\\Payment\\Interfaces\\' . $payment;
            $pay = new $contor($this);
            return $pay->Notify($order,$data,$config);
        } else {
            $this->response('0001', [], '支付平台等待接入');
        }
    }

    public function Ratify(){
        $data = $this->request->param();
        xn_log($this->request->get_client_ip().' '.json_encode($data), 'pay_ratify');
        list($payment_id,$order_id) =explode('_', xn_decrypt($this->request->param('3','')));
        $config= $this->PayConfig->read(['id' => $payment_id]);
        $payment = ucfirst(strtolower($config['Channel']));
        if (!empty($config['Channel']) && is_file(__APPDIR__ . 'Controller/Payment/Interfaces/' . $payment . '.php')) {
            include_once _include(__APPDIR__ . 'Controller/Payment/Interfaces/' . $payment . '.php');
            $order = $this->Order->read(['ID'=>$order_id]);
            empty($order['ID']) AND $this->response('0001',[],'订单不存在');
            $this->OrderNotify->insert(['OrderID'=>$order['ID'],'CreateAT'=>time(),'Info'=>xn_json_encode($data),'IP'=>$this->request->get_client_ip(1)]);
            $contor = '\\Payment\\Interfaces\\' . $payment;
            $pay = new $contor($this);
            $data=$pay->Ratify($order,$data,$config);
            $this->View(get_defined_vars());
        } else {
            $this->response('0001', [], '支付平台等待接入');
        }
    }

    public function Fail(){

        $this->response('0001', [], '支付失败');

    }
}
