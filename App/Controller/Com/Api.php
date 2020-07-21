<?php

namespace Com;

use Ctrl\Controller;
use mysql_xdevapi\Exception;

Class Api extends Controller
{

    /**
     *获取公告文本内容
     */
    public function  getGmNoticeContent()
    {

        $id= $this->request->param('id');
        $res = $this->GmNotice->read(['id'=>$id]);
        return $res['content'];
    }
}
