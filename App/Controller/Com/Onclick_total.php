<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_total extends GameController {

    public function Index () {

        $sid = $this->request->param('sid','');
        $agent = $sid ? $this->User->read_by_username($sid):$this->default_user;
        $this->hspower($agent['id']);
        $child_list=[];
        $this->agent_child_list($agent['id'],$child_list);
        $kuozhan=$count=0;
        foreach ($child_list as $v){
                if($v['RoleID']==9){
                    $kuozhan+=1;
                    $agent_id[]=$v['id'];
                }else{
                    $count+=1;
                }
        }
        $agent_id[]=$agent['id'];
        $usernum = $this->Usr->count(['agent_id'=>$agent_id]);
        $usernumol = $this->Usr->count(['agent_id'=>$agent_id,'online'=>['>'=>0]]);

        return $this->View(get_defined_vars());
    }
}
