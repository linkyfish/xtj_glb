<?php

namespace Api;
use Ctrl\Controller;
Class Playerfeedback extends  Controller
{


    public function send()
    {

        $content = $this->request->param('content','');
        $usrid = $this->request->param('usrid','0');
        if(empty($content))
        {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'内容不能为空']);
        }
        /*
        $key = 'Playerfeedback_' .$this->request->get_client_ip();
        $islock = $this->User->CacheGet($key);
        $time = time();
        if (!empty($islock['time'])) {
            return json_encode(['code'=>0,'success'=>false,'msg'=>'操作太快']);
        }
        $this->User->CacheSet($key, ['time' => $time], 60);
        */


        $data['add_time'] = time() ;
        $data['content'] = string_remove_xss($content)  ;
        $data['usrid'] = $usrid ;
        $id = $this->SyeFeedBack->insert($data);
        if($id >0 )
        {
            return json_encode(['code'=>1,'success'=>true,'msg'=>'提交成功']);
        }
        else{
            return json_encode(['code'=>0,'success'=>false,'msg'=>'提交失败']);
        }
    }




}