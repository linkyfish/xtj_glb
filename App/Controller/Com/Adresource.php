<?php

namespace Com;

use Ctrl\GameController;

Class Adresource extends GameController
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
        $list = $this->Adresource->GetList([],['id'=>-1],$page,$limit);
        foreach ($list['results'] as &$row){
			$row['times'] = date('Y-m-d H:i:s',$row['times']);
        }
        $this->response('0000',$list);
    }
    public function Index_POST()
    {
        $page = max(1,$this->request->param('page',1));
        $agent_id = $this->request->param('agent_id',0);
        if(!$this->isadmin){
            $agent_id=$this->token['id'];
        }
        $list = $this->Adresource->GetList([],['id'=>-1],$page,10);
        $this->response('0000',$list);
    }

    public function Add()
    {
        $content = $this->request->param('content','');
        $content = xn_html_safe($content);
        $this->CheckEmpty([$content],['留言内容']);
        $images = $this->request->param('images','#');
        $image_array=[];

        if(is_file(__UPFDIR__.'/'.$images)){
            $image_array[]=$images;
        }
        $this->Adresource->insert([
            'times'=>time(),
            'note'=>$content,
            'src'=>$images,
        ]);
        $this->response('0000');
    }


    public function Delete()
    {
        $id = $this->request->param('id','0');
        $this->Adresource->delete(['id'=>$id]);
        $this->response('0000');
    }




    public function Upload()
    {
    	$files = $this->request->files;
		empty($files['file']['tmp_name']) && $this->response('0001',[],'文件异常,禁止上传');
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
