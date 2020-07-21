<?php
/**
 * Created by PhpStorm.
 * User: yinhanmin
 * Date: 2019-03-09
 * Time: 08:49
 */

namespace Ashx;


use Ctrl\GameController;

class System extends GameController
{
    public function system_GetSysNoticeInfo()
    {
        $list = $this->SysNotice->select([], ['MsgID' => -1]);
        $admin = $this->agent_id_name;
        $data['results'] = [];
        foreach ($list as $k => $v) {
            $row['Rownum'] = $k + 1;
            $row['Index_NO'] = $v['MsgID'];
            $row['C_DateTime'] = date('Y-m-d H:i:s', $v['CreateAT']);
            $row['Account'] = isset($admin[$v['AdminID']]) ? $admin[$v['AdminID']] : '-';
            $row['MarqueeText'] = htmlspecialchars_decode($v['Content']);
            $data['results'][$k] = $row;
        }
        $this->response('0000', $data);
    }

    public function system_GetMoneySetInfo(){
        $data['results']=[];
        $this->response('0000', $data);
    }
    public function system_OpenCloseRecharge(){
        $id = $this->request->param('id',0);
        $info = $this->PayInfo->read(['payType'=>$id]);
        if($info['status']==0){
            empty($info['paySupplier']) AND $this->response('0001',[],'未设置支付通道');
            empty($info['Price']) AND $this->response('0001',[],'未设置支付金额');
        }
        $this->PayInfo->update(['payType'=>$id],['status'=>$info['status']==1?0:1]);
        $this->response('0000',['type'=>$info['status']==1?0:1]);
    }

    public function system_ChangeRechargeID(){
        $this->needadmin(1);
        $id = str_replace('A_','',$this->request->param('id',''));
        $value = $this->request->param('value',0);
        $field = $this->request->param('field');
        $selectText = $this->request->param('selectText');
        $this->PayInfo->update(['payType'=>$id],[$field=>$value]);
        $this->response('0000',['NewValue'=>$selectText,'status'=>200]);
    }

    public function system_setMoneyMb(){
        $this->needadmin(1);
        $id = str_replace('price','',$this->request->param('keytype',''));
        $keyvalue = str_replace('，',',',$this->request->param('keyvalue'));

        $this->PayInfo->update(['payType'=>$id],['Price'=>$keyvalue]);
        $this->response('0000');
    }

    public function system_setChannelMoneyMb(){
        $this->needadmin(1);
        $keytype = str_replace('price','',$this->request->param('keytype',''));
        $id = str_replace('price','',$this->request->param('id',''));

        $keyvalue = str_replace('，',',',$this->request->param('keyvalue'));
        $sort = str_replace('，',',',$this->request->param('sortvalue'));
        $this->PayConfig->update(['id'=>$id],['Price'.$keytype=>$keyvalue,'Sort'.$keytype=>$sort]);
        $this->response('0000');
    }


    public function system_GetCurrentChangeRechargeInfo(){
        $data['results']=$this->PayInfo->select();
        $this->response('0000', $data);
    }


    public function system_GetRechargechannelList(){

    	$this->needadmin();
        $list = $this->PayConfig->select();
        foreach ($list as $k => $v) {
            $list[$k]['Rownum'] =  $k + 1;
        }
        $data['results'] = $list;
        $this->response('0000', $data);
    }

    public function system_PublishNotice()
    {
        $this->needadmin(1);
        $value = $this->request->param('Memo', '');
        $input = [
            'Content' => $value,
            'AdminID' => $this->token['id'],
            'CreateAT' => time(),
        ];

        !$this->SysNotice->insert($input) AND $this->response('0001', [], '录入失败');
        $content = "新增后台公告: [" . mb_substr($value, 0, 10) . "...]";
        $this->OperateLog->Add($this->token['id'], $this->token['id'], 1, $this->request->get_client_ip(1), $content);

        $this->response('0000');
    }

    public function system_DelNotice()
    {
        $this->needadmin(1);
        $id = $this->request->param('id', 0);
        !$this->SysNotice->delete(['MsgID' => $id]) AND $this->response('0001', [], '删除失败');
        $content = "删除了后台公告:{$id}";
        $this->OperateLog->Add($this->token['id'],$this->token['id'],1,$this->request->get_client_ip(1), $content);
        $this->response('0000');
    }


    public function system_GetSendMessageLog()
    {
        $sms_type = $this->request->param('sms_type',0);
        $pageIndex = $this->request->param('pageIndex', 1);
        $pageSize = $this->request->param('pageSize', 20);
        $cond =[];
        !empty($sms_type) AND $cond['sms_type']=$sms_type;
        $data =$this->SmsCode->GetList($cond,['id'=>-1],$pageIndex,$pageSize);
        $userid = arrlist_values($data['results'],'userid');
        $users =$userid ? arrlist_key_values($this->User->select(['id'=>$userid],[],'id,username'),'id','username'):[];
        foreach ($data['results'] as &$v){
            $v['username'] =isset($users[$v['userid']]) ? $users[$v['userid']]:'-';
            $v['create_time'] = date('Y-m-d H:i:s',$v['create_time']);
        }
        $this->response('0000',$data);
    }


        public function system_SendMessageToPlayer()
    {
        $Tel = $this->request->param('Tel','');
        $ModeID= $this->request->param('ModeID', 10);
        !isMobile($Tel) AND $this->response('0001',[],'请输入手机号码');
        !in_array($ModeID,[1,2,3,10])AND $this->response('0001',[],'不存在的模板');
        $type=[
            1=>'修改银行卡',2=>'下分确认',10=>'万能码',3=>'修改姓名',
        ];
        //万能码不需要判断存不存在
        if($ModeID!=10)
        {
            if($this->Account->read_one(['account'=>$Tel]) == null )
            {
                $this->response('0001',[],'会员不存在');
            }
        }

        $sms=[
            'phone'=>$Tel,
            'create_time'=>time(),
            'code'=>mt_rand(11111,99999),
            'sms_type'=>$type[$ModeID],
            'userid'=>$this->token['id'],
        ];

        $apiName  = 'HonglianSms';
//        switch ($ModeID)
//        {
//            case 1:
//            case 3:
//                $tpl ='xiugaiyinhang';
//                break;
//            case 2:
//                $tpl ='xiafen';
//                break;
//        }

        if($ModeID==10)
        {
            $this->SmsCode->insert($sms);
            $this->OperateLog->Add($this->token['id'],$this->token['id'],1,$this->request->get_client_ip(1),'生成短信'.$type[$ModeID].':'.$sms['code']);
            $this->response('0000');
        }

         $tpl =  'normal';
        $ret = $this->SmsCode->send($apiName,$tpl,$Tel, ['vars'=>['code'=>$sms['code']]]);
        if($ret['success'])
        {
            $this->SmsCode->insert($sms);
            $this->OperateLog->Add($this->token['id'],$this->token['id'],1,$this->request->get_client_ip(1),'发送短信'.$type[$ModeID].':'.$sms['code']);
            $this->response('0000');
        }
        $this->response('0001',[],$ret['msg']);

    }


    /**
     * 获取公告
     * @return mixed
     */
    public function  getNoticeContent()
    {
        $id = $this->request->param('id');
        $res = $this->GmNotice->read(['id'=>$id]);
        return $res['content'];
    }







}