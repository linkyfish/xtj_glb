<?php
/**
 * Session支持类 会有并发问题，所以不应大面积使用
 */


/**
 * Class Session
 * @property \Cache $cache
 * @property \Request $request
 * @property \swoole_http_response $response
 */
class Session
{
    /** @var string $id Session ID */
    public $sid;
    public $ip;
    public $useragent;
    public $sess_key;
    private $cache;

    /** @var array $sess Session content */
    public $sess;
    public $tablepre;
    public $sid_key;
    public $life;

    /**
     * Constructor
     *
     * @access public
     * @param string $id Session ID
     * @param string $sess Saved session content
     */
    public function __construct($cache, $request, $response)
    {
        $this->cache = $cache;
        $this->request = $request;
        $this->response = $response;
        $this->life = 3600;
        $this->tablepre =_CONF('cookie_tablepre');
        $this->sid_key = _CONF('session_id');
        empty($this->sid_key) AND $this->sid_key = 'session_id';
        $this->generateId();
        $this->open();

    }

    /**
     * Generate session id
     * Notice this function didn't check is this id already use or not
     *
     * @access public
     * @return string
     */
    public function generateId()
    {
        $session_id = $this->request->param($this->sid_key, '');
        $_session_id = explode('-',$session_id ? xn_decrypt($session_id):'');
        $this->ip = isset($this->request->server['REMOTE_ADDR'])?$this->request->server['REMOTE_ADDR']:'127.0.0.1';
        $this->useragent = isset($this->request->server['USER-AGENT'])? md5($this->request->server['USER-AGENT']):'';
        $this->sid = ($_session_id[2]&&$this->useragent==$_session_id[1]) ? $_session_id[2] : get_uniqid(32);
        $this->sess_key = $_session_id[2]==$this->sid ? $session_id : xn_encrypt(getut().'-'.$this->useragent.'-'.$this->sid.'-'.$this->ip);
    }

    public function sid()
    {
        return $this->sid;
    }

    public function has($offset)
    {
        return isset($this->sess[$offset]);
    }

    public function get($offset)
    {
        return isset($this->sess[$offset]) ? $this->sess[$offset] : null;
    }

    public function set($offset, $value)
    {
        $this->sess[$offset] = $value;
    }

    public function delete($offset)
    {
        unset($this->sess[$offset]);
    }

    public function clear()
    {
        $this->sess = [];
        $this->cache->delete('sess_' . $this->sid);
    }

    public function open()
    {
        if($this->sid){
			$sess = $this->cache->get('sess_' . $this->sid);
			$sess['sid'] = $this->sid;
			return $this->sess = $sess;
		}else{
        	return $this->sess=[];
		}

    }

    public function save()
    {
        is_callable([$this->response, 'cookie']) AND $this->response->cookie($this->tablepre . $this->sid_key, $this->sess_key, time() + $this->life, '/');
        return $this->cache->set('sess_' . $this->sid, $this->sess, $this->life);
    }

}