<?php

namespace Cash\Interfaces;

class Xinxin extends Order
{
	public $AppUrl;

    public function pay($order, $config,$NotifyUrl)
    {

        $this->AppUrl = $config['AppUrl'];
		$merCd=$config['AppID'];
		//商户密钥
		$merKey=$config['AppKey'];
		//加载私钥
		$pfxFileName = __APPDIR__."Controller/Cash/Interfaces/pfx/Xinxin.pfx";
		//私钥密码
		$pfxPassword='111111';

		//交易报文
		$params=  array();
		$params["versionNo"]="V01";
		$params["txnCd"]="F00000";
		$params["merCd"]=$merCd;
		$params["merOrderNo"]=$order['OrderID'];  //商户订单号
		$params["notifyUrl"]=$NotifyUrl;
		$params["accAttr"]="00";  //00 对私  01  对公
		$params["txnAmt"]=calculate($order['Money']);
		$params["txnSubmitTime"]= date("YmdHis");
		$params["bankName"]=urlencode($order['BankName']); //银行名称
		//支付参数
		$transData=array();
		$transData["cardNo"]=$order['BankCard']; //卡号
		$transData["cardName"]=urlencode($order['BankUser']);   //姓名
		$data= json_encode($transData);
		//私钥加密
		$transDataSignStr=$this->private_encrypt($pfxFileName,$pfxPassword,$data);
		//echo("私钥签名===========>".$transDataSignStr) ;
		//echo "\r\n";
		//存入交易报文
		$params["transData"]=$transDataSignStr;
		//交易报文签名
		$transStr=$this->getUrlStr($params);
		$transStr=$this->getSignature($transStr,$merKey);

		$params["signData"]=$transStr;
		$params["signType"]="HmacSHA1";
		//请求地址
		$sumUrl="/anpay";


		$resMap=$this->api($sumUrl,$params);
		xn_log(json_encode($resMap),'repay_log');
		if(empty($resMap["signDate"])){
			$this->app->CashLog->update(['LogID' => $order['CashLogID'],'Status'=>50], ['Status' => 10, 'DoTime' => 0,'Is_Df'=>0]);
			$this->app->RepayLog->update(['LogID'=>$order['LogID']],['Status'=>2,'Desc'=> urldecode($resMap["txnMsg"])]);
			$this->app->response('0001', [], urldecode($resMap['txnMsg']));
		}
		//保存系统返回签名
		$paySginDat=$resMap["signDate"];
		//移除不加密的参数
		unset($resMap["signType"]);
		unset($resMap["signDate"]);
		//验证签名
		$resSginStr=$this->getUrlStr($resMap);
		$mySignStr=$this->getSignature($resSginStr,$merKey);

		if($mySignStr===$paySginDat){

			//判断交易请求是否成功
			if($resMap["resCode"]=="00"){
				//交易数据
				$resData=$resMap["resData"];
				//公钥解密
				$resData=$this->public_decrypt($pfxFileName,$pfxPassword,$resData);
				$resData=json_decode($resData,true);
				//中文需要url解码
				//echo   urldecode($resData["txnMsg"]);
				$this->app->RepayLog->update(['LogID'=>$order['LogID']],['Status'=>1,'Desc'=> urldecode($resMap["txnMsg"])]);
				$this->app->CashLog->update(['LogID' => $order['CashLogID'],'Status'=>50], ['Status' => 20,'Is_Df'=>1]);
				$this->app->response('0000', [], urldecode($resData['txnMsg']));
			}else{
				$this->app->CashLog->update(['LogID' => $order['CashLogID'],'Status'=>50], ['Status' => 10,  'DoTime' => 0,'Is_Df'=>0]);
				$this->app->RepayLog->update(['LogID'=>$order['LogID']],['Status'=>2,'Desc'=> urldecode($resMap["txnMsg"])]);
				$this->app->response('0001', [], urldecode($resMap['txnMsg']));
			}

		}else{
			$this->app->response('0001', [], '签名验证失败,不匹配');
			echo  "";

		}

    }

    public function Notify($order, $data, $config)
    {
        if ($order['Status'] == 1) {
            return 'success';
        }

		//商户号
		$merCd=$config['AppID'];
		//商户密钥
		$merKey=$config['AppKey'];
		//获取异步通知参数
		$params=array();
		$params["resCode"]=$data["resCode"];
		$params["txnCd"]=$data["txnCd"];
		$params["merCd"]=$data["merCd"];
		$params["insCd"]=$data["insCd"];
		$params["merOrderNo"]=$data["merOrderNo"];
		$params["txnOrderNo"]=$data["txnOrderNo"];
		$params["txnAmt"]=$data["txnAmt"];
		$params["txnSubmitTime"]=$data["txnSubmitTime"];
		$params["txnDate"]=$data["txnDate"];
		$params["txnTime"]=$data["txnTime"];
		$params["txnSta"]=$data["txnSta"];
		$params["txnMsg"]=$data["txnMsg"];
		$params["versionNo"]=$data["versionNo"];
		$params["remarks"]=$data["remarks"];
		$transStr=$this->getUrlStr($params);
		$transStr=$this->getSignature($transStr,$merKey);

		//校验签名
		if($transStr == $data["signData"]){
			//更新订单
			$this->app->RepayLog->update(['LogID'=>$order['LogID']],['Status'=>1,'Desc'=> urldecode($data["txnMsg"])]);
			$this->app->CashLog->update(['LogID' => $order['CashLogID'],'Status'=>50], ['Status' => 20,'Is_Df'=>0]);
			return 'SUCCESS';
		}
		$this->app->CashLog->update(['LogID' => $order['CashLogID'],'Status'=>50], ['Status' => 10, 'AdminID' => 0, 'DoTime' => 0,'Is_Df'=>0]);
		$this->app->RepayLog->update(['LogID'=>$order['LogID']],['Status'=>2,'Desc'=> urldecode($data["txnMsg"])]);
        return 'error';
    }

	//组织需要加密的字符串
	public  function  getUrlStr($transMap){
		//排序
		ksort($transMap);

		$transStr="";
		$flag=1;
		foreach($transMap as $v=>$a)
		{
			if(sizeof($transMap)==$flag){
				$transStr= $transStr.$v."=".$a;
			}else{
				$transStr= $transStr.$v."=".$a."&";
			}
			$flag++;
		}

		return 	$transStr;
	}

	//HMAC-SHA1
	public function getSignature($str, $key) {
		$signature = "";
		if (function_exists('hash_hmac')) {
			$signature = bin2hex(hash_hmac("sha1", $str, $key, true));
		} else {
			$blocksize = 64;
			$hashfunc = 'sha1';
			if (strlen($key) > $blocksize) {
				$key = pack('H*', $hashfunc($key));
			}
			$key = str_pad($key, $blocksize, chr(0x00));
			$ipad = str_repeat(chr(0x36), $blocksize);
			$opad = str_repeat(chr(0x5c), $blocksize);
			$hmac = pack(
				'H*', $hashfunc(
					($key ^ $opad) . pack(
						'H*', $hashfunc(
							($key ^ $ipad) . $str
						)
					)
				)
			);
			$signature = bin2hex($hmac);
		}
		return $signature;
	}

	//私钥加密

	public function private_encrypt($pfxFileName,$pfxPassword,$data){

		$transDataSignStr="";
		$pkcs12 = file_get_contents($pfxFileName);
		if(openssl_pkcs12_read($pkcs12, $certs,$pfxPassword)){
			$privateKey = $certs['pkey'];
		}else{
			return $transDataSignStr;
		}

		openssl_private_encrypt($data, $transDataSignStr, $privateKey);
		$transDataSignStr=base64_encode($transDataSignStr);
		return $transDataSignStr;

	}

	//公钥解密
	public function public_decrypt($pfxFileName,$pfxPassword,$decryptData){

		//返回数据
		$transDataSignStr = "";
		//base64
		$decryptData=base64_decode($decryptData);
		//读取公钥
		$cer = file_get_contents($pfxFileName);

		if(openssl_pkcs12_read($cer, $certs,$pfxPassword)){
			$pub_key = $certs['cert'];
		}else{
			return $transDataSignStr;
		}

		$decryptionOK = openssl_public_decrypt($decryptData, $decryptSta, $pub_key, OPENSSL_PKCS1_PADDING);
		if($decryptionOK=== false){
			return "";
		}
		$transDataSignStr=$decryptSta;
		return $transDataSignStr;

	}



}