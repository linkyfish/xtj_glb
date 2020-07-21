<?php

namespace Com;

use Ctrl\Controller;
use mysql_xdevapi\Exception;

Class Qrcode extends Controller
{

    public function Index_cash()
    {
        $url = $this->User->CacheGet('dwz:cash');
        if (!$url) {
            $_url = 'http://'.createCode(5).'.'._CONF('JumpUrl01') . '/' . xn_encrypt('cash', 1) . '/';
            $durl = $this->longcode($_url );
            !empty($durl) AND $url = $durl;
            empty($url) AND $url = $_url;
            $durl && $this->User->CacheSet('dwz:cash', $url, 86400* 7);
        }
        return $this->View(get_defined_vars());
    }


    public function Index_cashBak()
    {
        $url = $this->User->CacheGet('dwz:cash');
        if (!$url) {
            $_url = 'http://'.createCode(5).'.'._CONF('JumpUrl01') . '/' . xn_encrypt('cash', 1) . '/';
            $durl = $this->longcode($_url );
            !empty($durl) AND $url = $durl;
            empty($url) AND $url = $_url;
            $durl && $this->User->CacheSet('dwz:cash', $url, 86400* 7);
        }
        return $this->View(get_defined_vars());
    }


    public function Index_cash2()
    {
        $url = _CONF('CashUrl')  ;
        if (!$url) {
            $durl = $this->longcode($_url );
            !empty($durl) AND $url = $durl;
            empty($url) AND $url = $_url;
            $durl  = $url ;
        }
        return $this->View(get_defined_vars());
    }



    public function Index_qrcode()
    {
        $var = trim($this->request->param('var',''));
        empty($var) && $this->response('0001',[],'错误');
        $url = $this->User->CacheGet('dwz:'.$var);
        if (!$url) {
            $_url = 'http://'.createCode(5).'.'._CONF('JumpUrl01') . '/' . xn_encrypt($var, 1) . '/';
            $durl = $this->longcode($_url );
            !empty($durl) AND $url = $durl;
            empty($url) AND $url = $_url;
            $durl && $this->User->CacheSet('dwz:'.$var, $url, 86400* 7);
        }
        return $this->View(get_defined_vars());
    }


    public function Index_share()
    {
        $code = $this->request->param('code');
        $cache = $this->request->param('cache');
        $url = $this->User->CacheGet('longcode:' . $code.$cache);
        if (strlen($url) < 6) {
            $durl = $this->longcode('http://'.createCode(5).'.'._CONF('JumpUrl01') . '/'.xn_encrypt('code_' . $code,1).'/');
            !empty($durl) AND $url = $durl;
            $durl && $this->User->CacheSet('longcode:' . $code, $url, 86400 * 7);
        }

        !empty($durl) AND $url = $durl;

        return $this->View(get_defined_vars(), 'Qrcode.Index');
    }


    public function Index_shareuseragent()
    {
        $input = $this->request->param('code');
        $user = $this->User->read(['guid'=>$input]);
        $code = $user['code'];
        $cache = $this->request->param('cache');
        $url = $this->User->CacheGet('longcode:' . $code.$cache);
        if (strlen($url) < 6) {
            $durl = $this->longcode('http://'.createCode(5).'.'._CONF('JumpUrl01') . '/'.xn_encrypt('code_' . $code,1).'/');
            !empty($durl) AND $url = $durl;
            $durl && $this->User->CacheSet('longcode:' . $code, $url, 86400 * 7);
        }

        !empty($durl) AND $url = $durl;

        return $this->View(get_defined_vars(), 'Qrcode.Index');
    }


    public function Index_url()
    {
        $_url = urldecode($this->request->param('url'));
        $url = $this->User->CacheGet('dwz:' . md5($_url));
        if (!$url) {
            $durl = $this->longcode($_url);
            !empty($durl) AND $url = $durl;
            empty($url) AND $url = $_url;
            $durl && $this->User->CacheSet('dwz:' . md5($_url), $url, 86400*7);
        }
        return $this->View(get_defined_vars(), 'Qrcode.Index');
    }


    /**
     * 获取用户短网址
     */
    public function getAdUserShortUrl()
    {
        /*
        $uid = $this->request->param('uid','-1');
        $agentInfo = $this->User->read(['username']);
        $url = $this->User->CacheGet('longcode:' . $uid);
        if (!$url) {
            $durl = $this->longcode('http://'.createCode(5).'.'._CONF('JumpUrl01') . '/'.xn_encrypt('code_' . $uid ,1).'/');
            !empty($durl) AND $url = $durl;
            $durl && $this->User->CacheSet('longcode:' . $uid, $url, 86400 * 7);
        }
        return $url;
        */

        $uid = $this->request->param('uid');
        $agentRes = $this->User->read(['guid' => $uid]);
        $code = $agentRes['code'];
        if(empty($code)){
            return '';
        }
        $url = $this->User->CacheGet('longcode:' . $code);
        if (!$url) {
            $durl = $this->longcode('http://'.createCode(5).'.'._CONF('JumpUrl01') . '/'.xn_encrypt('code_' . $code,1).'/');
            !empty($durl) AND $url = $durl;
            $durl && $this->User->CacheSet('longcode:' . $code, $url, 86400 * 7);
        }
        return $url;
    }

    public function Index_longcode()
    {
        $code = $this->request->param('code');
        $url = $this->User->CacheGet('longcode:' . $code);
        if (!$url) {
            $durl = $this->longcode('http://'.createCode(5).'.'._CONF('JumpUrl01') . '/'.xn_encrypt('code_' . $code,1).'/');
            !empty($durl) AND $url = $durl;
            $durl && $this->User->CacheSet('longcode:' . $code, $url, 86400 * 7);
        }
        return $this->View(get_defined_vars(), 'Qrcode.Index');
    }

    private function longcode($url, $i = 0)
    {
        //"http://yy.gongju.at/?a=addon&m=wxdwz2&token={$sess_token}&long=".$url
        $host = 'yy.gongju.at';
        $cli = new \Swoole\Coroutine\Http\Client($host, 80);
        $cli->setHeaders([
            'Host' => $host,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $cli->set(['timeout' => 60]);
        $data['token'] = _CONF('baofeng','84ff0b2b90a891c7826e3b2677388e80');
        $data['long'] = $url;
        $cli->post('/?a=addon&m=url2', $data);
        $errcode = [110 => true, 111 => true];
        if (!empty($errcode[$cli->errCode])) {
            $i += 1;
            $cli->close();
            if ($i < 5) {
                \co::sleep(1000);
                return $this->longcode($url, $i);
            } else {
                xn_log($url, 'longcode_error');
                return false;
            }
        }

        $arr= json_decode($cli->body,true);
        xn_log($url . ' ' . $cli->body, 'longcode');
        $cli->close();
        $arr['short']=='https://' && $arr['short']='';
        if (empty($arr['short'])) {
            $i += 1;
            $cli->close();
            if ($i < 5) {
                \co::sleep(1000);
                return $this->longcode($url, $i);
            } else {
                xn_log($url, 'longcode_error');
                return false;
            }
        }
        return $arr['short'];
    }

    public function Upload()
    {

        $files = $this->request->files;
        $this->is_safe_image($files['file']['tmp_name']);
        $data = file_copy($files['file'], 888);
        $this->response('0000', ['data' => $data], '上传成功', '', 200, 1);
    }

    public function  Index_uploadNoticeImg()
    {
        $files = $this->request->files;
        empty($files['file']['tmp_name']) && $this->response('0001',[],'文件异常,禁止上传');
        $this->is_safe_image($files['file']['tmp_name']);
        $id = $this->request->param('id');
        $token_id = $this->request->param('token_id',0);
        $data = file_copy($files['file'],$token_id);
        $data['src'] = '/'.str_replace('../../','',$data['src']);
        if(!empty($id))
        {
            if($data['src'])
            {
                $this->GmNotice->update(['id'=>$id],['picurl'=>$data['src']]);
            }
        }
        $data['id'] = $id;
        $this->response('0000', ['data'=>$data],'上传成功','',200,1);
    }

    public function is_safe_image($filename)
    {
        $s = file_read($filename);
        if (strpos($s, '<script') !== FALSE) {
            unset($s);
            $this->response('0001', [], '文件异常,禁止上传');
        }
        unset($s);
        return TRUE;
    }

    public function Index_refreshshare()
    {
        $code = $this->request->param('code');
        $cache = $this->request->param('cache');
        $url = '';
        if (strlen($url) < 6) {
            $durl = $this->longcode('http://'.createCode(5).'.'._CONF('JumpUrl01') . '/'.xn_encrypt('code_' . $code,1).'/');
            !empty($durl) AND $url = $durl;
            $durl && $this->User->CacheSet('longcode:' . $code, $url, 86400 * 7);
        }

        !empty($durl) AND $url = $durl;

        return $this->View(get_defined_vars(), 'Qrcode.Index');
    }

    public function Index_refreshall()
    {
        $adUserList = $this->User->select(['RoleID'=>9]);
        foreach ($adUserList as $item)
        {
            $durl =  $this->longcode('http://'.createCode(5).'.'._CONF('JumpUrl01') . '/'.xn_encrypt('code_' . $item['username'],1).'/');
            $durl && $this->User->CacheSet('longcode2:' . $item['username'], $durl, 86400 * 7);
        }
    }

}
