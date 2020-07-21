<?php

namespace Customer;

use Ctrl\Controller;

// hook customer_index_use.php

Class Index extends Controller
{

    public function index(){

        return $this->View();
    }
    // hook customer_index_start.php
    public function Join($server, $request)
    {
        if(!$request->fd){
            return false;
        }
        return $this->MsgClient->insert(['fd' => (int)$request->fd, 'wid' => (int)$server->worker_id, 'id' => 0, 'way' => 0, 'create_at' => time()]);
    }

    public function close($fd)
    {
        return $this->MsgClient->delete(['fd' => $fd]);
    }


    public function SetSignature(){
        $id = $this->request->param('id',0);
        $value = $this->request->param('value','');
        $this->Custom->update(['id'=>$id],['Signature'=>$value]);
        $this->OperateLog->Add($this->token['id'],$this->token['id'],1,$this->request->get_client_ip(1),'客服签名:'.$value);
        $this->response('0000');
    }

    public function Login($fd, $data)
    {

        if (!empty($data["kfid"])) {
            $this->MsgClient->insert(['fd' => $fd, 'wid' => $this->http_server->worker_id, 'id' => $data['way'] == 1 ? $data["usrid"] : $data["kfid"], 'way' => $data['way'], 'create_at' => time()]);
            $usr = $this->Usr->read(['usrid' => $data['usrid']]);
            if ($data['way'] == 1) {

                $friend = $this->MsgFriend->read(['agent_id'=>$data["kfid"],'usrid'=>$data['usrid']]);
                empty($friend['usrid']) && $this->MsgFriend->insert(['agent_id'=>$data["kfid"],'usrid'=>$data['usrid'],'create_at'=>time()]);
                $list = $this->MsgClient->select(['id' => $data["kfid"], 'way' => 2]);
                foreach ($list as $v) {
                    if ($this->http_server->exist($v['fd'])) {
                        $this->http_server->push($v['fd'], xn_json_encode(['action' => 'GetUser', 'username' => $usr['nickname'], 'usrid' => $data['usrid']]));
                    } else {
                        $this->MsgClient->delete(['fd' => $v['fd']]);
                    }
                }
            }
        }
    }


    public function kefumsg($fd, $data)
    {
        if (empty($data['msg'])) {
            return;
        }

        if (empty($data['from']) || empty($data["to"])) {
            return;
        }
        $list = $this->MsgClient->select(['id' => $data["to"], 'way' => 1]);
        $msgid = $this->Msg->insert(['FromID' => $data['from'], 'ToID' => $data["to"], 'Cid' => $data['cid'], 'Type' => 1, 'CreateAT' => time()]);
        $this->MsgInfo->insert(['ID' => $msgid, 'Msg' => $data['msg']]);
        foreach ($list as $v) {
            if ($this->http_server->exist($v['fd'])) {
                $this->http_server->push($v['fd'], xn_json_encode(['action' => 'GetMsg', 'cid' => $data['cid'], 'username' => $data['username'], 'id' =>$data['from'], 'msg' => $data['msg']]));
            } else {
                $this->MsgClient->delete(['fd' => $v['fd']]);
            }
        }
    }

    public function playermsg($fd, $data)
    {
        if (empty($data['msg'])) {
            return;
        }

        if (empty($data['from']) || empty($data['to'])) {
            return;
        }

        $list = $this->MsgClient->select(['id' => $data['to'], 'way' => 2]);
        $msgid = $this->Msg->insert(['FromID' => $data['from'], 'ToID' => $data['to'], 'Cid' => $data['cid'], 'Type' => 2, 'CreateAT' => time()]);
        $this->MsgInfo->insert(['ID' => $msgid, 'Msg' => $data['msg']]);


        foreach ($list as $v) {
            if ($this->http_server->exist($v['fd'])) {
                $this->http_server->push($v['fd'], xn_json_encode(['action' => 'GetMsg', 'cid' => $data['cid'], 'username' => $data['username'],'usrid' =>$data['from'], 'msg' => $data['msg']]));
            } else {
                $this->MsgClient->delete(['fd' => $v['fd']]);
            }
        }
    }

    // hook customer_index_end.php
}
