<?php

namespace Customer;

use Ctrl\Controller;

// hook customer_upload_use.php

Class Upload extends Controller
{

    // hook customer_upload_start.php

    public function Image()
    {

        $agent_id = $this->request->param('agent_id', '');
        if ($agent_id) {
            $id = $agent_id;//=xn_decrypt($agent_id,_CONF('salt'));
        } else {
            $usrid = $this->request->param('usrid', '');
            $id = $usrid;//=xn_decrypt($usrid,_CONF('salt'));
        }

        $files = $this->request->files;
        $this->is_safe_image($files['file']['tmp_name']);
        $data = file_copy($files['file'], $id);
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

    // hook customer_upload_end.php
}
