<?php

namespace Customer;

use Ctrl\Controller;

// hook customer_upper_use.php

Class Upper extends Controller
{

    // hook customer_upper_start.php

    public function Index()
    {
        $usrid = $this->request->param('usrid', 0);//玩家短号
        $t = $this->request->param('t', 0);//时间
        $id = $this->request->param('chatID', 0);//客服
        $this->CheckEmpty([$usrid, $id], ['会员号', '客服号']);
        $Custom = $this->Custom->read(['ID'=>$id]);
        if(empty($Custom['Signature']) || $Custom['Signature']=='#')
        {
           $this->response('0001',[],'客服尚未配置');
        }
        $this->response('0003','','',$Custom['Signature'].'&uid='.$usrid.'&name='.$usrid,302);
        empty($Custom['ID']) AND $this->response('0001',[],'信息有误，访问出错');
        $agent = $this->User->read_by_username($Custom['Passwd']);
        $player = $this->Usr->read(['usrid' => $usrid]);
//        $player['usrid'] = xn_encrypt($player['usrid'],_CONF('salt'));
//        $agent['id'] = xn_encrypt($agent['id'],_CONF('salt'));
        (empty($agent['id']) || empty($player['usrid']) || !in_array($agent['RoleID'], $this->admin_role_arr)) AND $this->response('0001', [], '信息有误，访问出错');
        return $this->View(get_defined_vars());
    }

    public function Player_log(){
        $usrid = $this->request->param('usrid',0);
        $agent_id = $this->request->param('agent_id',0);
        $page = $this->request->param('page',1);
        $size = $this->request->param('size',20);
        $cond=[
            'FromID'=>[$usrid,$agent_id],
            'ToID'=>[$usrid,$agent_id],
        ];
//        $player = $this->Usr->read(['usrid'=>$usrid]);
//        $player = $this->Usr->read(['usrid'=>$usrid]);
        $data = $this->Msg->GetList($cond,['ID'=>-1],$page,$size);
        $msgid= arrlist_values($data['results'],'ID');
        $msg = $msgid ? arrlist_key_values($this->MsgInfo->select(['ID'=>$msgid]),'ID','Msg'):[];

        foreach ($data['results'] as &$v){
            $v['content']=$v['Back']?'消息已撤回':$msg[$v['ID']];
            $v['timestamp']=$v['CreateAT']*1000;
        }
        $this->response('0000',$data);
    }

    // hook customer_upper_end.php
}
