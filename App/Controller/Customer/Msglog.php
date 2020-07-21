<?php

namespace Customer;

use Ctrl\GameController;

// hook customer_card_use.php

Class Msglog extends GameController
{

    // hook customer_card_start.php

    public function Index()
    {
        $id = $this->request->param('id', 0);
        $agent_id = $this->token['id'];
        return $this->View(get_defined_vars());
    }

    public function Log()
    {
        $usrid = $this->request->param('id', 0);
        $page = $this->request->param('page', 1);
        $size = $this->request->param('size', 20);
        $agent_id = $this->token['id'];
        $cond = [
            'FromID' => [$usrid, $agent_id],
            'ToID' => [$usrid, $agent_id],
        ];
        $player = $this->Usr->read(['usrid' => $usrid]);
        $data = $this->Msg->GetList($cond, ['ID' => -1], $page, $size);
        $msgid = arrlist_values($data['results'], 'ID');
        $msg = $msgid ? arrlist_key_values($this->MsgInfo->select(['ID' => $msgid]), 'ID', 'Msg') : [];

        foreach ($data['results'] as &$v) {
            $v['username'] = $player['nickname'] . '(' . $player['usrid'] . ')';
            $v['content'] = $v['Back'] ? '消息已撤回' : $msg[$v['ID']];
            $v['timestamp'] = $v['CreateAT'] * 1000;
        }
        $this->response('0000', $data);
    }

    public function back()
    {
        $cid = $this->request->param('cid', 0);
        $msg = $this->Msg->read(['Cid' => $cid, 'FromID' => $this->token['id']]);
        $this->Msg->update(['Cid' => $cid, 'FromID' => $this->token['id']], ['Back' => 1]);
        $list = $this->MsgClient->select(['id' => $msg["ToID"], 'way' => 1]);
        foreach ($list as $v) {
            if ($this->http_server->exist($v['fd'])) {
                $this->http_server->push($v['fd'], xn_json_encode(['action' => 'GetBack', 'id' => $msg["FromID"], 'cid' => $cid]));
            } else {
                $this->MsgClient->delete(['fd' => $v['fd']]);
            }
        }
        $this->response('0000');
    }

    public function friend()
    {
        $list = $this->MsgFriend->select(['agent_id' => $this->token['id']]);
        $usrid = arrlist_values($list, 'usrid');
        $usr = $this->Usr->select(['usrid' => $usrid], [], 'usrid,nickname,sign,total_uppoints');

        $data = [
            ["groupname" => "万户", "id" => 1, "online" => 0, "list" => []],
            ["groupname" => "千户", "id" => 2, "online" => 0, "list" => []],
            ["groupname" => "百户", "id" => 3, "online" => 0, "list" => []]
        ];
        foreach ($usr as $v) {
            if ($v['total_uppoints'] > 10000 * $this->bl) {
                $data[0]['list'][] =  [
                    "username" => $v['nickname'] . '(' . $v['usrid'] . ')'
                    , "id" => $v['usrid']
                    , "avatar" => "../../logo.png"
                    , "sign" => $v['sign']
                ];
            } elseif ($v['total_uppoints'] > 1000 * $this->bl) {
                $data[1]['list'][] =  [
                    "username" => $v['nickname'] . '(' . $v['usrid'] . ')'
                    , "id" => $v['usrid']
                    , "avatar" => "../../logo.png"
                    , "sign" => $v['sign']
                ];
            } else {
                $data[2]['list'][] = [
                    "username" => $v['nickname'] . '(' . $v['usrid'] . ')'
                    , "id" => $v['usrid']
                    , "avatar" => "../../logo.png"
                    , "sign" => $v['sign']
                ];
            }
        }
        $this->response('0000', ['data'=>['friend'=>$data,'mine'=>['username'=>'上分客服','id'=>$this->token['id'],'avatar'=>"../../logo.png",'sign'=>$this->token['Signature']]]]);
    }
    // hook customer_card_end.php
}
