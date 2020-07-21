<?php
/**
 * Created by PhpStorm.
 * User: yinhanmin
 * Date: 2019-03-09
 * Time: 08:48
 */

namespace Ashx;


use Ctrl\GameController;

class Chart extends GameController
{

    public function chart_chat()
    {
        $type = $this->request->param('type', '');
        $sDate = $this->request->param('sDate', date('Y-m-01'));
        $eDate = $this->request->param('eDate', date('Y-m-d'));
        $sid = $this->request->param('user' );
        $agent = $sid ? $this->User->read_by_username($sid): $this->default_user;
        $this->hspower($agent['id']);
        $rbl = $this->request->param('rbl', '');
        $rbl = $rbl == 1 ? 0 : 1;
        if ($type == 'AgentChart') {
            $data['ViewTYPE'] = $rbl;

//            $child_list = [];
//            $this->agent_child_list($agent['id'], $child_list);
//            $agent_id = arrlist_values($child_list, 'id');
//            $agent_id[] = $agent['id'];
            $start = $sDate . ' 00:00:00';
            $end = $eDate . ' 23:59:59';
//            $usr = $this->Usr->select(['agent_id' => $agent_id], [], 'usrid');
//            $usr_id = arrlist_values($usr, 'usrid');
//            $cond = [];
//            $cond['usrid'] = $usr_id;

            $data['results'] = $this->MoneyTrans->stat_game_date_new($start, $end,$agent['id']);
        }
        $this->response('0000', $data);
    }

    public function chart_AgentChart()
    {
        $this->chart_chat();
    }
}