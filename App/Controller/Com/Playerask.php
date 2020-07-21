<?php

namespace Com;

use Ctrl\GameController;

Class Playerask extends GameController
{

    public function Index()
    {
        $agent_id = $this->request->param('agent_id',0);
        return $this->View(get_defined_vars());
    }
    public function More()
    {

        $page = max(1,$this->request->param('page',1));
        $limit = max(1,$this->request->param('limit',10));

        //获取推广号
        $son_zhuhao_user = $this->User->find_one(['parent_id' => $this->token['id'],'RoleID'=>10], ['share_rate' => -1]);
        $adList= $this->User->select(['parent_id' => $son_zhuhao_user['id'],'RoleID'=>9], ['share_rate' => -1],'id',1,1000000);
        $ids = arrlist_values($adList,'id');
        if(count($ids)>0)
        {
            $cond['touid'] = $ids;
            $ask = $this->FeedBack->GetList($cond,['ID'=>-1],$page,$limit);
            $admin = $this->agent_id_name;
            foreach ($ask['results'] as &$v){
                $v['username'] = $admin[$v['touid']].'('.$v['touid'].')';
                $v['question'] = string_remove_xss(   $v['question']);
                if(empty($v['reply']))
                {
                    $v['reply'] = '未回复';
                }
            }
            $this->response('0000',$ask);
        }

        $this->response('0000',[]);
    }
    public function Index_POST()
    {
        $page = max(1,$this->request->param('page',1));
        $agent_id = $this->request->param('agent_id',0);
        $ask = $this->FeedBack->GetList(['id'=>$agent_id],['id'=>-1],$page,10);
        foreach ($ask['results'] as &$v){
            $v['username'] = $agent_id .' 留言';
            if(empty($v['reply']))
            {
                $v['reply'] = '<span>未回复</span>';
            }
        }
        $this->response('0000',$ask);
    }

    public function reply()
    {
        $content = $this->request->param('content','');
        $agent_id = $this->request->param('agent_id',0);
        $usrid = $this->request->param('usrid',0);
        $content = xn_html_safe($content);
        $this->CheckEmpty([$content],['留言内容']);
        $data['reply'] =  $content;
        $res = $this->FeedBack->find_one(['id'=>$agent_id]);
        if(!$res)
        {
            $this->response('0001','非法操作');
        }
        if($res['userid'] !=$usrid)
        {
            $this->response('0001','不能回复非自己的玩家');
        }

        $this->FeedBack->update(['id'=>$agent_id],$data);
        $this->OperateLog->Add($this->token['id'],$this->token['id'],1,$this->request->get_client_ip(1),'留言');
        $this->response('0000');
    }


}
