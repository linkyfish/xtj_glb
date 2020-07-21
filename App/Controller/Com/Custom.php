<?php

namespace Com;

use Ctrl\GameController;

Class Custom extends GameController {

    public function Index () {
        $this->needadmin();
        $custorm = $this->User->select(['RoleID'=>101]);
        $custorm = arrlist_key_values($custorm,'username','username');
        return $this->View(get_defined_vars());
    }

    public function  Index_uploadCustomImg()
    {
        $files = $this->request->files;
        $id = $this->request->param('id');
        if(empty($id))
        {
            $id = time();
        }
        $data = file_copy($files['file'], '1');
        $data['src'] = '/'.str_replace('../../','',$data['src']);
        if(!empty($id))
        {
            if($data['src'])
            {
                $this->Custom->update(['ID'=>$id],['head_img'=>$data['src']]);
            }
        }
        $data['id'] = $id;
        $this->response('0000', ['data'=>$data],'上传成功','',200,1);
    }


}
