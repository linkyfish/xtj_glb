<?php
// hook public_function_before.php

function createCode($codelen = 5)
{
	$random = 'abcdefghkmnprstuvwxyzABCDEFGHKMNPRSTUVWXYZ123456789';
	$_len = strlen($random) - 1;
	$code = '';
	for ($i = 0; $i < $codelen; $i++) {
		$code .= $random[mt_rand(0, $_len)];
	}
	return $code;
}

function  getApiToken($time )
{
    return md5($time.'zb');
}

function post_api($http, $data, $i = 1)
{

    list(, , $host, $remote_server) = explode('/', $http);
    list($host, $port) = explode(':', $host);

    $cli = new \Swoole\Coroutine\Http\Client($host, $port);
    $cli->setHeaders([
        'Host' => $host,
        'Accept' => 'text/html,application/xhtml+xml,application/xml',
        'Accept-Encoding' => 'gzip',
    ]);
    $cli->set(['timeout' => 60]);
    $cli->post('/'.$remote_server, $data);
    $errcode = [110 => true, 111 => true];
    if (!empty($errcode[$cli->errCode])) {
        $i += 1;
        $cli->close();
        if ($i < 5) {
            xn_log($http . ' ' . xn_json_encode($data). ' ' . $cli->body, 'api_error');
            co::sleep(1);
            return post_api($http, $data, $i);
        } else {
            xn_log($http . ' ' . xn_json_encode($data). ' ' . $cli->body, 'api_error');
            return false;
        }
    }
    $edata = xn_json_decode($cli->body);
    xn_log($http . ' ' . xn_json_encode($data) . ' ' . $cli->body, 'api');
//    if (empty($edata) || $edata['success'] != 1) {
//
//    }
    $cli->close();
    return $edata;
}


function http_get_api($http,$headerSet=null)
{
    list(, , $host, $remote_server) = explode('/', $http);
    list($host, $port) = explode(':', $host);
    $port =  strpos($http, 'https://') !== false?443:80;
    $cli = new \Swoole\Coroutine\Http\Client($host, $port,$port==443?true:false);
    $header = [
        'Host' =>$host,
        'Accept' => 'text/html,application/xhtml+xml,application/xml',
        'Accept-Encoding' => 'gzip',
    ];
    if(is_array($headerSet))
    {
        $header = array_merge($header,$headerSet);
    }
    else{
        if($headerSet !=null)
        {
            array_push($header,$headerSet);
        }
    }
    $cli->setHeaders($header);
    $cli->set([ 'timeout' =>5]);
    $cli->get('/'.$remote_server);
    $cli->close();
    return $cli->body;
}





function post_formdata($url,$postData)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL,$url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS,
        $postData);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $server_output = curl_exec($ch);
    curl_close ($ch);
    return $server_output;
}

function mid($n, $min, $max) {
    if($n < $min) return $min;
    if($n > $max) return $max;
    return $n;
}

function humandate($timestamp,$time) {
	static $custom_humandate = NULL;
	$lan = array(
		'month_ago'=>'月前',
		'day_ago'=>'天前',
		'hour_ago'=>'小时前',
		'minute_ago'=>'分钟前',
		'second_ago'=>'秒前',
	);

    if($custom_humandate === NULL) $custom_humandate = function_exists('custom_humandate');
    if($custom_humandate) return custom_humandate($timestamp, $lan);
    $seconds = $time - $timestamp;

    if($seconds > 31536000) {
        return date('Y-n-j', $timestamp);
    } elseif($seconds > 2592000) {
        return floor($seconds / 2592000).$lan['month_ago'];
    } elseif($seconds > 86400) {
        return floor($seconds / 86400).$lan['day_ago'];
    } elseif($seconds > 3600) {
        return floor($seconds / 3600).$lan['hour_ago'];
    } elseif($seconds > 60) {
        return floor($seconds / 60).$lan['minute_ago'];
    } else {
        return $seconds.$lan['second_ago'];
    }
}

function humannumber($num) {

    static $custom_humannumber = NULL;
    if($custom_humannumber === NULL) $custom_humannumber = function_exists('custom_humannumber');
    if($custom_humannumber) return custom_humannumber($num);

    $num > 100000 && $num = ceil($num / 10000).'万';
    return $num;
}

function humansize($num) {

    static $custom_humansize = NULL;
    if($custom_humansize === NULL) $custom_humansize = function_exists('custom_humansize');
    if($custom_humansize) return custom_humansize($num);

    if($num > 1073741824) {
        return number_format($num / 1073741824, 2, '.', '').'G';
    } elseif($num > 1048576) {
        return number_format($num / 1048576, 2, '.', '').'M';
    } elseif($num > 1024) {
        return number_format($num / 1024, 2, '.', '').'K';
    } else {
        return $num.'B';
    }
}

function isMobile($mobile)
{
    return preg_match('#^1[\d]{10}$#', $mobile) ? true : false;
}

function pwd($pass, $token)
{
    return md5(md5($pass) . md5($token));
}


function CheckSubstrs($substrs, $text)
{
    foreach ($substrs as $substr)
        if (false !== strpos($text, $substr)) {
            return true;
        }
    return false;
}

/**
 * 签名算法
 * $appKey = 'test';
 * $appSecret = 'test';
 * $sessionkey= 'test';
 * //参数数组
 * $paramArr = array(
 * 'app_key' => $appKey,
 * 'session_key' => $sessionkey,
 * 'method' => 'taobao.user.seller.get',
 * 'format' => 'json',
 * 'v' => '2.0',
 * 'sign_method'=>'md5',
 * 'timestamp' => date('Y-m-d H:i:s'),
 * );
 *
 * //生成签名
 * $sign = createSign($paramArr);
 * //组织参数
 * $strParam = createStrParam($paramArr);
 * $strParam .= 'sign='.$sign;
 */
function createSign($paramArr, $appSecret)
{
    $sign = $appSecret;
    ksort($paramArr);
    foreach ($paramArr as $key => $val) {
        if ($key != '' && $val != '') {
            $sign .= $key . $val;
        }
    }
    $sign .= $appSecret;
    $sign = strtoupper(md5($sign));
    return $sign;
}



//组参函数

function createStrParam($paramArr)
{

    $strParam = '';
    foreach ($paramArr as $key => $val) {
        if ($key != '' && $val != '') {
            $strParam .= $key . '=' . urlencode($val) . '&';
        }
    }
    return $strParam;
}

function calculate($num,$bl=1000)
{
    return $num ? sprintf("%.2f", bcdiv($num, $bl,3)):0.00;

}

function calculateSub2($num,$bl=1000)
{
    return  bcdiv($num, $bl,2);
}

function string_remove_xss($html) {
    preg_match_all("/\<([^\<]+)\>/is", $html, $ms);

    $searchs[] = '<';
    $replaces[] = '&lt;';
    $searchs[] = '>';
    $replaces[] = '&gt;';

    if ($ms[1]) {
        $allowtags = 'img|a|font|div|table|tbody|caption|tr|td|th|br|p|b|strong|i|u|em|span|ol|ul|li|blockquote';
        $ms[1] = array_unique($ms[1]);
        foreach ($ms[1] as $value) {
            $searchs[] = "&lt;".$value."&gt;";

            $value = str_replace('&amp;', '_uch_tmp_str_', $value);
            $value = string_htmlspecialchars($value);
            $value = str_replace('_uch_tmp_str_', '&amp;', $value);

            $value = str_replace(array('\\', '/*'), array('.', '/.'), $value);
            $skipkeys = array('onabort','onactivate','onafterprint','onafterupdate','onbeforeactivate','onbeforecopy','onbeforecut','onbeforedeactivate',
                'onbeforeeditfocus','onbeforepaste','onbeforeprint','onbeforeunload','onbeforeupdate','onblur','onbounce','oncellchange','onchange',
                'onclick','oncontextmenu','oncontrolselect','oncopy','oncut','ondataavailable','ondatasetchanged','ondatasetcomplete','ondblclick',
                'ondeactivate','ondrag','ondragend','ondragenter','ondragleave','ondragover','ondragstart','ondrop','onerror','onerrorupdate',
                'onfilterchange','onfinish','onfocus','onfocusin','onfocusout','onhelp','onkeydown','onkeypress','onkeyup','onlayoutcomplete',
                'onload','onlosecapture','onmousedown','onmouseenter','onmouseleave','onmousemove','onmouseout','onmouseover','onmouseup','onmousewheel',
                'onmove','onmoveend','onmovestart','onpaste','onpropertychange','onreadystatechange','onreset','onresize','onresizeend','onresizestart',
                'onrowenter','onrowexit','onrowsdelete','onrowsinserted','onscroll','onselect','onselectionchange','onselectstart','onstart','onstop',
                'onsubmit','onunload','javascript','script','eval','behaviour','expression','style','class');
            $skipstr = implode('|', $skipkeys);
            $value = preg_replace(array("/($skipstr)/i"), '.', $value);
            if (!preg_match("/^[\/|\s]?($allowtags)(\s+|$)/is", $value)) {
                $value = '';
            }
            $replaces[] = empty($value) ? '' : "<" . str_replace('&quot;', '"', $value) . ">";
        }
    }
    $html = str_replace($searchs, $replaces, $html);

    return $html;
}

function string_htmlspecialchars($string, $flags = null) {
    if (is_array($string)) {
        foreach ($string as $key => $val) {
            $string[$key] = string_htmlspecialchars($val, $flags);
        }
    } else {
        if ($flags === null) {
            $string = str_replace(array('&', '"', '<', '>'), array('&amp;', '&quot;', '&lt;', '&gt;'), $string);
            if (strpos($string, '&amp;#') !== false) {
                $string = preg_replace('/&amp;((#(\d{3,5}|x[a-fA-F0-9]{4}));)/', '&\\1', $string);
            }
        } else {
            if (PHP_VERSION < '5.4.0') {
                $string = htmlspecialchars($string, $flags);
            } else {
                if (!defined('CHARSET') || (strtolower(CHARSET) == 'utf-8')) {
                    $charset = 'UTF-8';
                } else {
                    $charset = 'ISO-8859-1';
                }
                $string = htmlspecialchars($string, $flags, $charset);
            }
        }
    }

    return $string;
}




function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0)
{
	// 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
	$ckey_length = 4;

	// 密匙
	$key = md5($key ? $key : '123');

	// 密匙a会参与加解密
	$keya = md5(substr($key, 0, 16));
	// 密匙b会用来做数据完整性验证
	$keyb = md5(substr($key, 16, 16));
	// 密匙c用于变化生成的密文
	$keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length) : substr(md5(microtime()), -$ckey_length)) : '';
	// 参与运算的密匙
	$cryptkey = $keya . md5($keya . $keyc);
	$key_length = strlen($cryptkey);
	// 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
	//解密时会通过这个密匙验证数据完整性
	// 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
	$string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) : sprintf('%010d', $expiry ? $expiry + time() : 0) . substr(md5($string . $keyb), 0, 16) . $string;
	$string_length = strlen($string);
	$result = '';
	$box = range(0, 255);
	$rndkey = [];
	// 产生密匙簿
	for ($i = 0; $i <= 255; $i++) {
		$rndkey[$i] = ord($cryptkey[$i % $key_length]);
	}
	// 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
	for ($j = $i = 0; $i < 256; $i++) {
		$j = ($j + $box[$i] + $rndkey[$i]) % 256;
		$tmp = $box[$i];
		$box[$i] = $box[$j];
		$box[$j] = $tmp;
	}
	// 核心加解密部分
	for ($a = $j = $i = 0; $i < $string_length; $i++) {
		$a = ($a + 1) % 256;
		$j = ($j + $box[$a]) % 256;
		$tmp = $box[$a];
		$box[$a] = $box[$j];
		$box[$j] = $tmp;
		// 从密匙簿得出密匙进行异或，再转成字符
		$result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
	}
	if ($operation == 'DECODE') {
		// 验证数据有效性，请看未加密明文的格式
		if ((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) && substr($result, 10, 16) == substr(md5(substr($result, 26) . $keyb), 0, 16)) {
			return substr($result, 26);
		} else {
			return '';
		}
	} else {
		// 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
		// 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
		return $keyc . str_replace('=', '', base64_encode($result));
	}
}


function encrypt($num)
{
	return createCode(3) . base_convert($num, 10, 30);
}

function dncrypt($string)
{
	return base_convert(substr($string, 3), 30, 10);
}

// hook public_function_after.php

?>