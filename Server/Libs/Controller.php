<?php

namespace Server\Libs;

/**
 * Class Controller
 * @package Server\Libs
 * @property \Request $request
 * @property \Session $session
 * @property \swoole_http_response $_response
 * @property \Server\Server $server
 * @property \swoole_websocket_server $http_server
 *
 * //IDE_LOAD_START
 *
 * //IDE_LOAD_END
 */
Class Controller
{

    public $server;

    /**
     * User: zhixiang
     *  Explain:
     *  -
     *
     * Ctrl constructor.
     *
     * @param $conf
     */
    public function __construct($server, $route, \Request $request, $response)
    {
        $this->server = $server;
        $this->http_server = $server->http_server;
        $this->is_ajax = strtolower($request->_S('X-REQUESTED-WITH')) == 'xmlhttprequest';
        $this->_method = $request->_S('REQUEST_METHOD');
        $this->token = [];
        $this->assign = [];
        $this->route = $route;
        $this->request = $request;
        $this->_response = $response;
    }

    /**
     * User: zhixiang
     *  Explain:
     *  -
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($_ENV['_models'][$name])) {
            return $this->$name = $_ENV['_models'][$name];
        } elseif (class_exists("\\Model\\{$name}Model", false)) {
            $model = "\\Model\\{$name}Model";
            $_ENV['_models'][$name] = new $model($this->server);
            return $this->$name = $_ENV['_models'][$name];
        } else {
            throw new \Exception($name.'Model禁止访问', '500');
        }
    }

    public function setcookie($name, $value, $expire = 3600, $path = '/')
    {
        return $this->_response->cookie(_CONF('cookie_tablepre') . $name, $value, time() + $expire, $path);
    }

    public function response($code = '0000', $data = [], $msg = '', $url = '', $status = 200, $json = 0)
    {
        // hook common_response_before.php
        $json = $this->is_ajax ? $this->is_ajax : $json;

        if ($status == 301 || $status == 302) {
            $result = $url;
        } else {
            // hook common_response_code_before.php
            $_msg = $msg?$msg:(isset($_ENV['err_code'][$code]) ? $_ENV['err_code'][$code]:'');

            if ($json == 1) {
                $result = [
                    'resp_code' => $code,
                    'msg' => $_msg,
                ];

                // hook common_response_data_before.php
                is_array($data) AND $result = array_merge($result, $data);
                if(!isset($data['success'])){
                 $code == '0000' AND $result['success'] = true;
                 $code != '0000' AND $result['success'] = false;
                }
                // hook common_response_data_json_before.php
                $result = xn_json_encode($result);
                // hook common_response_data_json_after.php
            } else {
                $error_file = _CONF('error_file', __CONDIR__ . '500.php');
                $result = !empty($_msg) ? ($msg ? (is_array($msg) ? $_msg . '(' . $msg[0] . ')' : $msg) : $_msg) : ($msg ? $msg : '操作成功');
                if (is_file($error_file)) {
                    $status = 5001;
                    if ($this->assign) {
                      extract($this->assign, EXTR_OVERWRITE);
                    }
                    ob_start();
                    include _include($error_file);
                    $result = ob_get_contents();
                    ob_end_clean();
                }
            }
        }

        // hook common_response_after.php
        throw new \Exception($result, $status);
    }


    //投递异步任务
    public function PostTask($action, $data, $_controller = 'Task')
    {

        return $this->http_server->task([
            'controller' => $_controller,
            'action' => $action,
            'data' => $data
        ]);
    }

    public function end()
    {

        if (!empty($this->session)) {
            $this->session->save();
        }

    }
}

?>