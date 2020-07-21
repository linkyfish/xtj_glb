<?php

namespace Com;

use Ctrl\GameController;

Class Com_namelist extends GameController {

    public function Index () {
        $this->needadmin(1);
        return $this->View();
    }

    public function Index_lost(){
        $this->needadmin(1);
        $data['results']=$this->LostControl->select();
        $this->response('0000',$data);
    }
    public function Index_win(){
        $this->needadmin(1);
        $data['results']=$this->WinControl->select();
        $this->response('0000',$data);
    }

    public function Index_namelist(){
        $this->needadmin(1);
        $username = $this->request->param('username','');
        $page = $this->request->param('page',1);
        $limit = $this->request->param('limit',10);
        $cond=[];
        if(!empty($username)){
            $user = $this->findPlayer($username);
            $cond['player_id']=$user['usrid'];
        }
        $list = $this->SuperControlList->GetList($cond,  [],  $page, $limit,  '*', 1,'group by player_id');
        foreach ( $list['results'] as $key => &$v)
        {
            $v['action'] .= '<a type="button" class="btn btn-primary btn-xs" title="'.$v['usrid'].'" onfocus="this.blur();" onclick="onSupuerControlSeting(this,'.$v['player_id'].',window.location.reload);">设置</button>';
            $v['action'] .= '<button type="button" class="btn btn-primary btn-xs" title="'.$v['usrid'].'" onfocus="this.blur();" onclick="onControlDel(this,'.$v['player_id'].',window.location.reload);">移除名单</button>';
            $v['times'] = date('Y-m-d H:i:s');
            $data[] = $v;
        }
        $list['results'] =  $data;
        $this->response('0000',$list);
    }

    public function Index_lost_edit(){
        $this->needadmin(1);
        $id =$this->request->param('id',0);
        $field=$this->request->param('field','');
        $value=$this->request->param('value','');
        empty($id) AND $this->response('0001',[],'编号不存在');
        $this->LostControl->update(['id'=>$id],[$field=>$value]);
        $this->response('0000');
    }


    public function Index_win_edit(){
        $this->needadmin(1);
        $id =$this->request->param('id',0);
        $field=$this->request->param('field','');
        $value=$this->request->param('value','');
        empty($id) AND $this->response('0001',[],'编号不存在');
        $this->LostControl->update(['id'=>$id],[$field=>$value]);
        $this->response('0000');
    }
    public function Index_namelist_edit(){
        $this->needadmin(1);
        $id =$this->request->param('id',0);
        $field=$this->request->param('field','');
        $value=$this->request->param('value','');
        empty($id) AND $this->response('0001',[],'编号不存在');
        $this->SuperControlList->update(['id'=>$id],[$field=>$value]);
        $this->response('0000');


    }
    public function   Index_supuercontrol_add()
    {
        $this->needadmin(1);
        $username = $this->request->param('username','');
        $postData = $this->request->post;
        $cond=[];
        if(!empty($username)){
            $user = $this->findPlayer($username);
            $cond['player_id']=$user['usrid'];
        }

        if(!$user)
        {
            $this->response('0001', [], '用户不存在.');
        }

        $count = $this->SuperControlList->read(['player_id'=>$user['usrid']],'' );//$cond, $select = '*'

        if($count )
        {
            $this->response('0001', [], '用户已经在超控名单中.');
        }

        //添加到单控
        $data['player_id'] = $user['usrid'];
        $data['start_val'] = $user['start_val']?intval($user['start_val']):-9999999;
        $data['end_val'] = $user['end_val']?intval($user['end_val']):9999999;
        $data['code'] = $user['code']?intval($user['code']):'H1';
        $data['control_val'] = 100;
        $data['times'] = time();
        $id = $this->SuperControlList->insert($data);
        $subVal = $this->calControlVal($user['usrid']);
        empty($id) AND $this->response('0001', [], '添加失败.');
        $this->Usr->update(['usrid'=>$user['usrid']],['super_control'=>1,'sup_control_val'=>$data['control_val']]);
        //通知服务端更新单控
        $port = '9090';
        if($user['roomid'] >1)
        {
            $port = $this->RoomBank->getGameHttpPortById($user['roomid']);
        }
        $update_url = 'http://' . _CONF('gameServerIp') .$port.'/'._CONF('UpdatePlayerSuperControl_Url') ;
        $formData = [
            'playerid'=>$user['usrid'],
            'super_control'=>1,
            'sup_control_val'=>$subVal
        ];
        $response =  post_api($update_url, $formData);
        $this->response('0000',$data);
    }


    /**
     * 配置项目
     */
    public function   Index_supuercontrol_itemadd()
    {
        $this->needadmin(1);
        $username = $this->request->param('user','');
        $postData = $this->request->post;
        $cond=[];
        if(!empty($username)){
            $user = $this->findPlayer($username);
            $cond['player_id']=$user['usrid'];
        }
        if(!$user)
        {
            $this->response('0001', [], '用户不存在.');
        }

        $val = intval($postData['val']);
        if($val>10)
        {
            $this->response('0001', [], '控制值不能超过10.');
        }
        if($val <=0)
        {
            $this->response('0001', [], '控制值不能小于0');
        }
        //单控
        $data['player_id'] = $user['usrid'];
        $data['start_val'] = $postData['start_val']?intval($postData['start_val']):-9999999;
        $data['end_val'] = $postData['end_val']?intval($postData['end_val']):9999999;
        $data['code'] = $postData['code']?$postData['code']:'H1';

        $data['control_val'] = $val*100;
        $data['times'] =time();
        $id = $this->SuperControlList->insert($data);

        empty($id) AND $this->response('0001', [], '添加失败.');
        //通知服务端更新单控
        $port = '9090';
        if($user['roomid'] >1)
        {
            $port = $this->RoomBank->getGameHttpPortById($user['roomid']);
        }
        $update_url = 'http://' . _CONF('gameServerIp') .$port.'/'._CONF('UpdatePlayerSuperControl_Url') ;
        $formData = [
            'playerid'=>$user['usrid'],
            'super_control'=>1,
            'sup_control_val'=>$data['control_val']
        ];
         post_api($update_url, $formData);
        $this->response('0000',$data);
    }



    public function Index_superItemList()
    {

        $this->needadmin(1);
        $username = $this->request->param('username','');
        $page = $this->request->param('page',1);
        $limit = $this->request->param('limit',10);
        $cond=[];
        if(!empty($username)){
            $user = $this->findPlayer($username);
            $cond['player_id']=$user['usrid'];
        }
        $list = $this->SuperControlList->GetList($cond,  [],  $page, $limit,  '*', 1,'group by player_id');
        foreach ( $list['results'] as $key => &$v)
        {
            $v['action'] .= '<button type="button" class="btn btn-warning btn-xs" title="'.$v['usrid'].'" onfocus="this.blur();" onclick="onSupuerControlSeting(this,'.$v['player_id'].');">设置</button>';
            $v['action'] .= '<button type="button" class="btn btn-warning btn-xs" title="'.$v['usrid'].'" onfocus="this.blur();" onclick="onControlDel(this,'.$v['player_id'].",reloadSuperNameList);\">删除</button>";
            $v['times'] = date('Y-m-d H:i:s',$v['times']);
            $data[] = $v;
        }
        $list['data'] =  $list;
        $this->response('0000',$list);

    }

    public function Index_superItemSettingList()
    {
        $this->needadmin(1);
        $username = $this->request->param('user','');
        $page = $this->request->param('page',1);
        $limit = $this->request->param('limit',10);
        $cond=[];
        if(!empty($username)){
            $user = $this->findPlayer($username);
            $cond['player_id']=$user['usrid'];
        }

        $list = $this->SuperControlList->GetList($cond,  [],  $page, $limit,  '*', 1);
        foreach ( $list['results'] as $key => &$v)
        {
            $v['control_val'] = $v['control_val']/100;
            $v['action'] .= '<button type="button" class="btn btn-warning btn-xs" title="'.$v['usrid'].'" onfocus="this.blur();" onclick="onSupeuControlItemDel(this,'.$v['id'].',window.location.reload);">删除</button>';
            $data[] = $v;
        }
        $list['data'] =  $list;
        $this->response('0000',$list);

    }

    /**
     * 编辑超控白名单设置
     */
    public function   Index_supuercontrol_edit()
    {
        $this->needadmin(1);
        $id =$this->request->param('id',0);
        $value=$this->request->param('value','0');
        $field=$this->request->param('field','');
        if($value>10)
        {
            $this->response('0001', [], '控制值不能超过10.');
        }
        if($value <=0)
        {
            $this->response('0001', [], '控制值不能小于0');
        }

        $updateData = [
            $field=>$value*100
        ];
        empty($id) AND $this->response('0001',[],'编号不存在');
        $this->SuperControlList->update(['id'=>$id],$updateData);
        $res  = $this->SuperControlList->read(['id'=>$id]);
        $userRes =  $this->Usr->read(['usrid'=>$res['player_id']]);

        //计算单控的范围
        $subVal  = $this->calSuperControlVal($userRes);
        //及时更新
        $roomInfo  = $this->RoomBank->read(['roomID'=>$userRes['roomid']]);
        if($roomInfo)
        {
            $port = $roomInfo['port'];
        }
        else{
            $port = '9090';
        }

        $update_url = 'http://' . _CONF('gameServerIp') .':'.$port.'/'._CONF('UpdatePlayerSuperControl_Url');
        $formData = [
            'playerid'=>$res['player_id'],
            'super_cotrol'=>1,
            'sup_control_val'=>$subVal,
        ];
         post_api($update_url, $formData);
        $this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '更新用户单控失败【' . $res['player_id'] . '】');
        $this->response('0000',['msg'=>'更新单控成功']);
    }


    private function  calSuperControlVal($userRes)
    {

        $subVal = 1;
        $user = $userRes;
        $total_uppoints = $user['total_uppoints'];
        $page = 1 ;
        $limit = 1000;
        $cond = ['player_id'=>$user['usrid']] ;
        $controlSettingList   = $this->SuperControlList->GetList($cond,  [],  $page, $limit,  '*', 1);
        $list = $controlSettingList['results'];
        //玩家输钱

        $total_cash = $user['total_cash'];
        $value = calculate($total_cash-$total_uppoints);
        //最终概率
        foreach($list as $item)
        {
            if($value>$item['start_val']&&$value<$item['end_val'])
            {
                $subVal = $item['control_val'];
                break;
            }
            else{
                if($value == $item['start_val'] ||   $value == $item['end_val'])
                {
                    $subVal = $item['control_val'];
                    break;
                }

            }
        }

        return $subVal;
    }



    private function  calControlVal($userRes)
    {

        $superControlList   = $this->SuperControlList->select();
        $value =  calculate($userRes['total_win'])   ;// $total_cash-($chips+$total_uppoints);
        //超控概率
        foreach($superControlList as $item)
        {
            if($item['player_id'] == $userRes['usrid'])
            {
                if($value>$item['start_val']&&$value<$item['end_val'])
                {
                    $subVal = $item['control_val'];
                    break;
                }
                else{
                    if($value == $item['start_val'] ||   $value == $item['end_val'])
                    {
                        $subVal = $item['control_val'];
                        break;
                    }
                }
            }

        }
        return $subVal;
    }

    /**
     * 全局风控部iao配置
     */
    public function   Index_lost_control_edit()
    {
        $this->needadmin(1);
        $id =$this->request->param('id',0);
        $value=$this->request->param('value','');
        $field=$this->request->param('field','');

        $updateData = [
            $field=>$value
        ];

        empty($id) AND $this->response('0001',[],'编号不存在');
        $preRes = $this->LostControl->read(['id'=>$id]);
        $this->LostControl->update(['id'=>$id],$updateData);
        $userRes =  $this->Usr->read(['id'=>$preRes['player_id']]);

        //计算单控的范围
        $subVal  = $this->calControlVal($userRes);
        //及时更新
        $roomInfo  = $this->RoomBank->read(['roomID'=>$userRes['roomid']]);
        if($roomInfo)
        {
            $port = $roomInfo['port'];
        }
        else{
            $port = '9090';
        }
        $update_url = 'http://' . _CONF('gameServerIp') .':'.$port.'/'._CONF('UpdatePlayerControl_Url');
        $formData = [
            'playerid'=>$preRes['player_id'],
            'single_cotrol'=>1,
            'control_val'=>$subVal,
        ];
        $response =  post_api($update_url, $formData);
        if($response['success']!=1)
        {
            $this->response('0001', [], '更新用户单控失败请稍后再试'.$response['msg']);
        }

        $this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '更新用户单控失败【' . $preRes['player_id'] . '】');
        $this->response('0000');
    }


    /**
     * 全局赢分风控
     */
    public function   Index_win_control_edit()
    {
        $this->needadmin(1);
        $id =$this->request->param('id',0);
        $value=$this->request->param('value','');
        $field=$this->request->param('field','');
        $updateData = [
            $field=>$value
        ];
        empty($id) AND $this->response('0001',[],'编号不存在');
        $preRes = $this->WinControl->read(['id'=>$id]);
        $this->WinControl->update(['id'=>$id],$updateData);
        $userRes =  $this->Usr->read(['id'=>$preRes['player_id']]);

        //计算单控的范围
        $subVal  = $this->calControlVal($userRes);
        //及时更新
        $roomInfo  = $this->RoomBank->read(['roomID'=>$userRes['roomid']]);
        if($roomInfo)
        {
            $port = $roomInfo['port'];
        }
        else{
            $port = '9090';
        }
        $update_url = 'http://' . _CONF('gameServerIp') .':'.$port.'/'._CONF('UpdatePlayerControl_Url');
        $formData = [
            'playerid'=>$preRes['player_id'],
            'single_cotrol'=>1,
            'control_val'=>$subVal,
        ];
        $response =  post_api($update_url, $formData);

        if($response['success']!=1)
        {
            $this->response('0001', [], '更新用户单控失败请稍后再试'.$response['msg']);
        }
        $this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), '更新用户单控失败【' . $preRes['player_id'] . '】');
        $this->response('0000');
    }


}
