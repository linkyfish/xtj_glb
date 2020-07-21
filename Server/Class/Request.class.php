<?php
/**
 * HTTP请求封装
 */

/**
 * Class Controller
 * @package Server\Libs
 *  @property \swoole_http_request $req
 */

class Request
{

    /** @var object $sw_request Swoole request object */
    private $request;
    private $req;

    /** @var object $session Cached session */


    /** @var string $module Module */
    public $module = null;
    /** @var string $controller Controoler */
    public $controller = null;
    /** @var string $action Action */
    public $action = null;

    public $time = 0;
    public $starttime = 0;
    public $startmemory = 0;


    /** @var array Same as swoole */
    public $get;
    public $post;
    public $server;
    public $header;
    public $cookie;
    public $files;
    public $route;
    public $cfile;
    public $ip;
    public $iplogg;



    public function __construct(swoole_http_request $req)
    {
        $this->time = time();
        $this->starttime = getut();
        $this->startmemory = getum();
        //var_dump($req);
        $this->req = $req;
        $this->get = (array)$req->get;
        $this->post = (array)$req->post;
        $this->server = array_change_key_case(array_merge((array)$req->header, (array)$req->server), CASE_UPPER);
        $this->header = (array)$req->header;
        $this->cookie = (array)$req->cookie;
        $this->files = (array)$req->files;


        $request_url = str_replace('.do', '', $req->server["request_uri"]);
        $request_url = ltrim(str_replace('/?', '/', $request_url), '/');



		$routeInfo = $_ENV['dispatcher']->dispatch($req->server['request_method'], $request_url);

		if ($routeInfo[0] == FastRoute\Dispatcher::FOUND) {
			$route = explode('/', $routeInfo[1]); // 获得处理函数
			$arr = $routeInfo[2];
		} else {
			$request = explode('?', $request_url);
			$route = explode('/', $request[0]);
			$arr = [];
			if (!empty($request[1])) {
				parse_str($request[1], $arr);
			}
		}
		//$this->request = array_merge($this->get, $this->post, $this->cookie, $route, $arr);

        $this->request = array_merge($this->get, $this->post, $this->cookie, $route, $arr);
        for ($ri = 1; $ri <= count($route) / 2; $ri++) {
            isset($route[$ri * 2 + 2]) AND $this->request[$route[$ri * 2 + 1]] = $route[$ri * 2 + 2];
        }

        $sid_key = _CONF('session_id');
        $tablepre = _CONF('cookie_tablepre');
        empty($sid_key) AND $sid_key = 'session_id';
        if (!empty($this->get['access_token'])||!empty($this->post['access_token'])) {
            $this->cookie[$tablepre . $sid_key] = $this->request['access_token'];
            $this->server['X-REQUESTED-WITH'] = 'xmlhttprequest';
        }

        $i = max(2, count($route));
        $max = $GLOBALS['conf']['route_max'] ? $GLOBALS['conf']['route_max'] : 3;
        $last = $i > $max ? min($i, $max - 1) : min($i, $max - 1);

        $action = !empty($route[$last]) ? ucfirst(strtolower(trim($this->param($last)))) : 'Index';

        $arr = explode('.', $action);
        $url_suffix = _CONF('url_suffix');

        if (isset($arr[1])) {
            if (in_array($arr[1], (array)$url_suffix, 1)) {
                $action = $arr[0];
            }
        }

        $cfile = '';
        $_route = [];
        for ($ii = 0; $ii < $last; $ii++) {
            $f = !empty($route[$ii]) ? ucfirst(strtolower(trim($this->param($ii)))) : 'Index';
            $arr = explode('.', $f);
            if (!empty($arr[1])) {
                if (in_array($arr[1], (array)$url_suffix, 1)) {
                    $f = $arr[0];
                }
            }
            $_route[] = $f;
            $cfile .= '/' . $f;
        }


        $this->route = $_route;
        $this->action = $action;


    }

    /**
     * Get original post body
     *
     * @access public
     * @return string
     */
    public function rawContent()
    {
        return $this->req->rawContent();
    }


    public function get_client_ip($type = 0) {
        $type       =  $type ? 1 : 0;
        $ip     =   $this->_S('REMOTE_ADDR');
        $newIp=$this->_S('X-FORWARDED-FOR');
        $ip4=$this->_S('CF-PSEUDO-IPV4');
        if(!empty($newIp)){
            $newIp=explode(",",$newIp);
            $ip=trim($newIp[0]);
			if (filter_var($ip, FILTER_VALIDATE_IP,FILTER_FLAG_IPV6)) {
				$ip=trim($newIp[1]);
			}
        }
		!empty($ip4) && $ip=$ip4;
        $long = sprintf("%u",ip2long($ip));
        $ip   = $long ? array($ip, $long) : array('0.0.0.0', 0);
        return $ip[$type];
    }

	/**
	 * IPV6 地址转换为整数
	 * @param $ipv6
	 * @return string
	 * */
	function ip2long6($ipv6)
	{
		$ip_n = inet_pton($ipv6);
		$bits = 15; // 16 x 8 bit = 128bit
		$ipv6long = '';
		while ($bits >= 0) {
			$bin = sprintf("%08b", (ord($ip_n[$bits])));
			$ipv6long = $bin . $ipv6long;
			$bits--;
		}
		return gmp_strval(gmp_init($ipv6long, 2), 10);
	}

	function long2ip_v6($dec)
	{
		if (function_exists('gmp_init')) {
			$bin = gmp_strval(gmp_init($dec, 10), 2); //10进制 -> 2进制
		} elseif (function_exists('bcadd')) {
			$bin = '';
			do {
				$bin = bcmod($dec, '2') . $bin; //10进制 -> 2进制，获取$dec/2的余数
				$dec = bcdiv($dec, '2', 0); // dec/2的值，0表示小数点后位数
			} while (bccomp($dec, '0'));
		} else {
			// trigger_error('GMP or BCMATH extension not installed!', E_USER_ERROR);
			return 'GMP or BCMATH extension not installed!';
		}

		$bin = str_pad($bin, 128, '0', STR_PAD_LEFT); // 给2进制值补0
		$ip = array();
		for ($bit = 0; $bit <= 7; $bit++) {
			$bin_part = substr($bin, $bit * 16, 16); // 每16位分隔
			$ip[] = dechex(bindec($bin_part)); // 2进制->10进制->16进制
		}
		$ip = implode(':', $ip);
		// inet_pton:将可读的IP地址转换为其压缩的in_addr表示形式
		// inet_ntop:将打包的Internet地址转换为可读的表示形式
		return inet_ntop(inet_pton($ip));
	}

    public function param($key = '', $defval = '', $safe = true)
    {
        if (empty($key) && $key !== 0) {
            $val = $this->request;
        } else {
            if (!isset($this->request[$key]) || ($key === 0 && empty($this->request[$key]))) {
                if (is_array($defval)) {
                    return array();
                } else {
                    return $defval;
                }
            }
            $val = $this->request[$key];
            $val = $this->param_force($val, $defval, $safe);
        }

        return $val;
    }

    public function _S($key, $defval = '', $safe = true)
    {
        if (!isset($this->server[$key]) || ($key === 0 && empty($this->server[$key]))) {
            if (is_array($defval)) {
                return array();
            } else {
                return $defval;
            }
        }
        $val = $this->server[$key];
        $val = $this->param_force($val, $defval, $safe);
        return $val;
    }


    /*
    仅支持一维数组的类型强制转换。
    param_force($val);
    param_force($val, '');
    param_force($val, 0);
    param_force($arr, array());
    param_force($arr, array(''));
    param_force($arr, array(0));
    */
    public function param_force($val, $defval, $safe = true)
    {
        if (is_array($defval)) {
            $defval = empty($defval) ? '' : $defval[0]; // 数组的第一个元素，如果没有则为空字符串
            if (is_array($val)) {
                foreach ($val as &$v) {
                    if (is_array($v)) {
                        $v = $defval;
                    } else {
                        if (is_string($defval)) {
//$v = trim($v);
                            $safe AND !__GCP__ && $v = addslashes($v);
                            !$safe AND __GCP__ && $v = stripslashes($v);
                            $safe AND $v = htmlspecialchars($v);
                        } else {
                            $v = intval($v);
                        }
                    }
                }
            } else {
                return array();
            }
        } else {
            if (is_array($val)) {
                $val = $defval;
            } else {
                if (is_string($defval)) {
//$val = trim($val);
                    $safe AND !__GCP__ && $val = addslashes($val);
                    !$safe AND __GCP__ && $val = stripslashes($val);
                    $safe AND $val = htmlspecialchars($val);
                } else {
                    $val = intval($val);
                }
            }
        }

        return $val;
    }


    /**
     * Finish request, release resources
     *
     * @access public
     */
    public function end()
    {

        $this->get = null;
        $this->post = null;
        $this->server = null;
        $this->header = null;
        $this->cookie = null;
        $this->files = null;
        $this->req = null;
        $this->request = null;
        $this->route = null;

    }


}
