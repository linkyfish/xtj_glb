<?php

namespace Com;


use Ctrl\Controller;
use Ctrl\GameController;

Class Safecode extends GameController {

    // hook index_index_start.php

    public function Index()
    {
        return $this->View();
    }

    public function Index_setsafecode()
    {
        $code = $this->request->param('safe_code','0');
        if(strlen($code)!=4)
        {
            $this->response('0001', ['msg'=> '安全码长度为4位' ]);
        }
        $arr = [
            '1111',
            '1234',
            '4321',
            '2345',
            '0000',
            '2222',
            '3333',
            '4444',
            '5555',
            '6666',
            '7777',
            '8888',
            '9999',
        ];
        if(in_array($code,$arr))
        {
            $this->response('0001', ['msg'=> '安全码过于简单' ]);
        }
        $this->User->update(['id'=>$this->token['id']],['safe_code'=>$code]);
        $this->session->sess['user']['safe_code'] = $code;
        $msg = array('success' => true, 'msg' => '登录成功.', 'href' => "/com/userList.aspx?rid=" . $this->token['username'] . '&t=' . time());
        $this->response('0000',  $msg);
    }


}
