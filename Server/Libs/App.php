<?php

namespace Server;

use Server\Libs\Controller;

/**
 * Class App
 * @property Controller $ctrl
 * @property Controller $controller
 * @property \Db $db
 * @property \Cache $cache
 * @property \Server\Server $server
 * @property \swoole_websocket_server $http_server
 * @property \swoole_http_response $_response
 */
class App
{
    public $server;
    public $http_server;


    public function __construct($server)
    {
        $this->server = $server;
        $this->http_server = $server->http_server;
    }

    public function run($request, $response = '')
    {

        $request = new \Request($request);
        $route = $request->route;
        $action = $request->action;
        $requestRoute = json_encode($route);


        $k = implode('_', $route);
        $ctrl = '\\' . implode('\\', $route);

        $route[] = $action;
        if ($route[0] == 'Task') {
            (new Controller($this->server, $route, $request, $response))->response('0001', '', '404，您请求的资源不存在！5');
        }

        $response->header("Server", "Window 2030");
        $response->header("Access-Control-Allow-Origin", "*");
        $response->header('Access-Control-Allow-Headers","x-requested-with,content-type');
        $response->header("Access-Control-Allow-Methods", "HEAD, HEADER, GET, POST,PATCH, PUT, OPTIONS, DELETE");
        $response->header("Access-Control-Max-Age", "30");
        $response->header("Access-Control-Allow-Credentials", "true");
		$_route = implode('_', $route);
		$limiting = _CONF('limiting');
		$islock = false;
		if ($limiting > 0) {
			$num = $this->http_server->ip_lock->get($_route, 'num');
			if ($num >= $limiting) {
				$islock = true;
			} else {
				$limit_ok = true;
			}
			$this->http_server->ip_lock->incr($_route, 'num', 1);
		}
        try {
            if ($response) {
                $response->status(200);
                $response->header("Content-type", "text/html;charset=utf-8;");
            }
			if ($islock == true) {
				(new Controller($this->server, $route, $request, $response))->response('9999', [], '系统繁忙,' . $num . '个任务处理中,请稍候...');
			}

            $method = $request->server['REQUEST_METHOD'];
            $haction = $request->param('action', '');
            !empty($haction) AND $action = $action . '_' . $haction;

            //echo $action,"\r\n";
            if (class_exists($ctrl, false)) {
                $controller = new $ctrl($this->server, $route, $request, $response);
            } else {
                (new Controller($this->server, $route, $request, $response))->response('0002', '', '404，您请求的资源不存在！6'.$requestRoute);
            }

            if (is_callable([$controller, $action . '_' . $method])) {
                $action .= '_' . $method;
                if ($response) {
                    $end = $controller->$action();
                    $response->header("Use-Tim", ut($request->starttime));
                    $response->header("Use-Ram", um($request->startmemory));
                    $response->header("Use-route", $requestRoute);
                    $response->header("Use-action1", $action);
                    $response->header("Use-method", $method);
                    $response->write($end);
                    $response->end();
                } else {
                    echo $controller->$action();
                }

            } elseif (is_callable([$controller, $action])) {
                if ($response) {
                    $end = $controller->$action();
                    $response->header("Use-Tim", ut($request->starttime));
                    $response->header("Use-Ram", um($request->startmemory));
                    $response->header("Use-route", $requestRoute);
                    $response->header("Use-action2", $action);
                    $response->header("Use-method", $method);
                    $response->write($end);
                    $response->end();
                } else {
                    echo $controller->$action();
                }
            } else {
                $controller->response('0002', '', '404，您请求的资源不存在！');
            }
            $controller->end();
        } catch (\Exception $e) {
            $data = [];
            $data['code'] = $e->getCode();
            $data['data'] = $e->getMessage();
            !empty($controller) AND $controller->end();

            $response->header("Use-Tim", ut($request->starttime));
            $response->header("Use-Ram", um($request->startmemory));

            switch ($data['code']) {
                case 201:
                case 200:
                    //$data['data'] = xn_json_decode($data['data']);
                    //$data['data'] = xn_json_encode($data['data']);
                    if ($response) {
                        $response->status(200);
                        $response->header("Content-type", "text/json;charset=utf-8;");
                        $response->write($data['data']);
                        $response->end();
                    } else {
                        echo $data['data'];
                    }
                    break;
                case 301:
                case 302:
                    $response->redirect($data['data'], $data['code']);
                    break;
                case 5001:
                    if ($response) {
                        $response->status(200);
                        $response->header("Content-type", "text/html;charset=utf-8;");
                        $response->write($data['data']);
                        $response->end();
                    } else {
                        echo $data['data'];
                    }

                    break;

                case 500:
                    $_data = xn_json_decode($data['data']);
                    if ($response) {
                        $response->status(500);
                        if (!is_null($_data)) {
                            $data['data'] = $_data;
                            $data['data'] = xn_json_encode($data['data']);
                            $response->header("Content-type", "text/json;charset=utf-8;");
                        } else {
                            $response->header("Content-type", "text/html;charset=utf-8;");
                        }

                        $response->write($data['data']);
                        $response->end();
                    } else {
                        if (!is_null($_data)) {
                            $data['data'] = $_data;
                            $data['data'] = xn_json_encode($data['data']);
                        }
                        echo $data['data'];
                    }
                    break;
                case 501:
                    $response->status(500);
                    $response->header("Content-type", "text/html;charset=utf-8;");
                    $response->write($data['data']);
                    $response->end();
                    break;
                case 1001:
                    $data['data'] = xn_json_decode($data['data']);
                    $join = is_array($_ENV['mines'][$data['data']['data']['ext']]) ? implode(';', $_ENV['mines'][$data['data']['data']['ext']]) : $_ENV['mines'][$data['data']['data']['ext']];
                    $response->header('Content-Type', $join . ';charset=utf-8;');
                    $response->header('Content-Disposition', 'attachment;filename="' . $data['data']['data']['name'] . '";');
                    $response->status(200);
                    $response->sendfile($data['data']['data']['filename']);
                    break;
                case 2001:
                    $data['data'] = xn_json_decode($data['data']);
                    $response->status(200);
                    $response->header("Content-type", $data['data']['data']['type']);
                    $response->write(base64_decode($data['data']['data']['data']));
                    $response->end();
                    break;

                default:
                    $response->status(200);
                    $response->header("Content-type", "text/html;charset=utf-8;");
                    $response->write($data['data']);
                    $response->end();
            }
        }

        $request->end();
        $this->http_server->request_stat->incr(strtolower($k . '_' . $action), 'count', 1);
        $this->http_server->request_stat->incr(strtolower($k . '_' . $action), 'ms', ms_log($request->starttime));
		if ($limiting > 0) {
			$this->http_server->ip_lock->decr($_route, 'num', 1);
		}
        $request = null;
    }

    public function close($fd)
    {
        $request = new \Request(new \swoole_http_request());
        $controller = new \Customer\Index($this->server, ['Im', 'Index'], $request, []);
        $controller->close($fd);
    }

    public function talk($fd,$data){
        if($data['action']){
            $acc = $data['action'];
            $request = new \Request(new \swoole_http_request());
            $controller = new \Customer\Index($this->server, ['Im', 'Index'], $request, []);
            $controller->$acc($fd,$data['post']);
        }
        return false;
    }

    public function open($server, $request)
    {
        $request = new \Request($request);
        $controller = new \Customer\Index($this->server, ['Im', 'Index'], $request, []);

        return $controller->Join($server, $request);
    }

    public function task($r)
    {

        $route[] = 'Task';
        $route[] = $r['controller'] ? ucfirst($r['controller']) : 'Task';
        $action = $r['action'] ? ucfirst($r['action']) : 'Task';
        $ctrl = '\\' . implode('\\', $route);
        $k = implode('_', $route);
        $route[] = $action;

        $request = new \Request(new \swoole_http_request());
        $controller = new $ctrl($this->server, $route, $request, []);

        if (is_callable([$controller, $action])) {
            $data = $controller->$action($r['data']);
            $this->server->http_server->request_stat->incr(strtolower($k . '_' . $action), 'count', 1);
            $this->server->http_server->request_stat->incr(strtolower($k . '_' . $action), 'ms', ms_log($request->starttime));
            return $data;
        } else {
            return '方法不存在';
        }
    }


}

?>