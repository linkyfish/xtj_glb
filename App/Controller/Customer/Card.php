<?php

namespace Customer;

use Ctrl\GameController;

// hook customer_card_use.php

Class Card extends GameController
{

    // hook customer_card_start.php

    public function Index()
    {
        return $this->View(get_defined_vars());
    }

    public function Upload()
    {
        $this->needadmin();
        $id = $this->default_user['id'];
        $files = $this->request->files;
        $data = file_copy($files['file'], $id);
        $this->Images->insert(['UserID' => $id, 'Filename' => $data['src'], 'CreateAT' => time()]);
        $this->response('0000', ['data' => $data], '上传成功', '', 200, 1);
    }

    public function fast()
    {
        $data['results'] = $this->Images->select(['UserID' => $this->default_user['id']]);
        $this->response('0000', $data);
    }

    public function field()
    {
        $this->needadmin();
        $id = $this->request->param('id');
        empty($id) AND $this->response('0001',[],'缺少参数');
        $field = $this->request->param('field');
        $value = $this->request->param('value');
        $cond['id']=$id;
        !$this->isadmin AND $cond['UserID']=$this->token['id'];
        $this->Images->update($cond, [$field => $value]);
        $this->response('0000');
    }

    public function delete()
    {
        $this->needadmin();
        $id = $this->request->param('id');
        empty($id) AND $this->response('0001',[],'缺少参数');
        $cond['id']=$id;
        !$this->isadmin AND $cond['UserID']=$this->token['id'];
        $this->Images->delete($cond);
        $this->response('0000',[],'删除成功');
    }


    // hook customer_card_end.php
}
