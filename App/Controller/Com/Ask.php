<?php

namespace Com;

use Ctrl\GameController;

Class Ask extends GameController
{

    public function Index()
    {
        $agent_id = $this->request->param('agent_id',0);
        return $this->View(get_defined_vars());
    }
    public function More()
    {
        $this->needadmin();
        $page = max(1,$this->request->param('page',1));
        $limit = max(1,$this->request->param('limit',10));
        $ask = $this->Ask->GetList(['IsFirst'=>1],['ID'=>-1],$page,$limit);
        $admin = $this->agent_id_name;
        foreach ($ask['results'] as &$v){
            $v['username'] = $admin[$v['AgentID']].'('.$v['AgentID'].')';
            $v['Message'] = string_remove_xss($v['Message']);
        }
        $this->response('0000',$ask);
    }
    public function Index_POST()
    {
        $page = max(1,$this->request->param('page',1));
        $agent_id = $this->request->param('agent_id',0);
        if(!$this->isadmin){
            $agent_id=$this->token['id'];
        }
        $ask = $this->Ask->GetList(['AgentID'=>$agent_id],['ID'=>-1],$page,10);
        $admin = $this->agent_id_name;
        foreach ($ask['results'] as &$v){
            $v['username'] = $v['AdminID'] ? $admin[$v['AdminID']].' 回复':$admin[$v['AgentID']].' 留言';
        }
        $this->response('0000',$ask);
    }

    public function Add()
    {
        $content = $this->request->param('content','');
        $content = xn_html_safe($content);
        $this->CheckEmpty([$content],['留言内容']);
        $images =explode(',', $this->request->param('images',''));
        $image_array=[];
        foreach ($images as $image){
            $_image = str_replace(__UPPATH__,'',$image);
            //echo __UPFDIR__.'/'.$_image;
            if(is_file(__UPFDIR__.'/'.$_image)){
                $image_array[]=$image;
            }
        }
        $images=implode(',',$image_array);
        $this->Ask->update(['AgentID'=>$this->token['id'],'IsFirst'=>1],['IsFirst'=>0]);
        $this->Ask->insert([
            'AgentID'=>$this->token['id'],
            'CreateAT'=>time(),
            'IsFirst'=>1,
            'Message'=>$content,
            'Images'=>$images,
        ]);
        $this->OperateLog->Add($this->token['id'],$this->token['id'],1,$this->request->get_client_ip(1),'留言');
        $this->response('0000');
    }

    public function Reply()
    {
        $this->needadmin();
        $agent_id = $this->request->param('agent_id',0);
        $agent_id==$this->token['id'] && $this->response('0001',[],'不能回复自己');
        $content = $this->request->param('content','');
        $content = xn_html_safe($content);
        $this->CheckEmpty([$agent_id,$content],['代理','内容']);
        $images =explode(',', $this->request->param('images',''));
        $image_array=[];
        foreach ($images as $image){
            $_image = str_replace(__UPPATH__,'',$image);
            if(is_file(__UPFDIR__.'/'.$_image)){
                $image_array[]=$image;
            }
        }
        $images=implode(',',$image_array);
        $this->Ask->insert([
            'AgentID'=>$agent_id,
            'AdminID'=>$this->token['id'],
            'CreateAT'=>time(),
            'Message'=>$content,
            'Images'=>$images,
        ]);
        $this->OperateLog->Add($this->token['id'],$agent_id,1,$this->request->get_client_ip(1),'回复留言');
        $this->response('0000');
    }


    public function Upload()
    {
        $files = $this->request->files;
        $this->is_safe_image($files['file']['tmp_name']);
        $data = file_copy($files['file'], $this->token['id']);
        $this->response('0000', ['data'=>$data],'上传成功','',200,1);
    }

    public function is_safe_image($filename) {
        $s = file_read($filename);
        if(strpos($s, '<script') !== FALSE) {
            unset($s);
            $this->response('0001',[],'文件异常,禁止上传');
        }
        unset($s);
        return TRUE;
    }


}
