<?php

namespace Com;

use Ctrl\GameController;

Class Tree extends GameController
{

    public function Index()
    {
        $this->needadmin();
        $sid = $this->request->param('sid', '');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        return $this->View(get_defined_vars());
    }

    public function Index_data(){
        $this->needadmin();
        $sid = $this->request->param('sid');
        $agent =$sid ? $this->User->read_by_username($sid) :$this->default_user;
        $child_list = [];
        $this->agent_child_list($agent['id'], $child_list);
        $agent_id = arrlist_values($child_list, 'id');
        $agent_id[] = $agent['id'];
        $cond =['is_deleted' => 0,'id' => $agent_id];
        $list = $this->User->select($cond, [], 'id,parent_id,share_rate,balance,username');
        $data =[];
        foreach ($list as $v){
            $row=[
                'id'=>$v['id'],
                'parent_id'=>$v['parent_id'],
                'username'=>$v['username'] .' -  占成:'.$v['share_rate'],
            ];
            $data[]=$row;
        }

        $this->response('0000',['data'=>$data]);
    }


}
