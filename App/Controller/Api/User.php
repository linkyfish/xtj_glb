<?php

namespace Api;
use Ctrl\Controller;
Class User extends  Controller
{

    public function  index()
    {
        $cond['endtime'] = ['>='=>'2020-1-1'];
        $result = $this->AdCopy->select($cond, [], '*', 0, 0, '');
        return json_encode($result);
    }
    public function Register()
    {

        $inputData = $this->request->post;


        $userName = '';
        if(isset($inputData['phone']))
        {
            $userName = trim($inputData['phone']);
        }
        if(isset($inputData['account']))
        {
            $userName = trim($inputData['account']);
        }

        if(empty($inputData['code']))
        {
            $inputData['code'] = '000000';
        }

        $userExist = $this->Account->read(['account'=>$userName]);
        if($userExist)
        {
            return json_encode(['code'=>3,'success'=>false,'msg'=>'用户已存在']);
        }

        $adRes = $this->User->read(['code'=>$inputData['code']]);
        if (!isset($adRes['id'])) {
            return json_encode(['code'=>2,'success'=>false,'msg'=>'推广号不存在']);
        }
        if(!in_array($adRes['RoleID'],[9,111]))
        {
            return json_encode(['code'=>2,'success'=>false,'msg'=>'推广号不存在']);
        }

        if(!isset($inputData['v_code']))
        {

            return json_encode(['code'=>2,'success'=>false,'msg'=>'短信验证码不能为空']);
        }

        if(!isset($inputData['password']))
        {
            return json_encode(['code'=>2,'success'=>false,'msg'=>'密码不能为空']);
        }

        if(!isset($inputData['machine_code']))
        {

            return json_encode(['code'=>2,'success'=>false,'msg'=>'机器码不能为空']);
        }

        $smsRes = $this->SmsCode->count(['phone'=>$inputData['account'],'Status'=>0,'code'=>$inputData['v_code']]);
        if ($smsRes < 1) {
            return json_encode(['code'=>2,'success'=>false,'msg'=>'验证码不正确']);

        }
        $tempRand = rand(1, 999999);
        $data['account'] = $userName;
        $data['phone'] = trim($userName);
        $data['times'] = date('Y-m-d H:i:s');
        $data['password'] = md5($tempRand.$inputData['password'].date("Y-m-d"));
        $data['checktoken'] = $tempRand.'/'.$data['times'];
        $data['ip'] = $this->request->get_client_ip() ;//$_SERVER["REMOTE_ADDR"];
        $data['merchant_code'] = $inputData['code'];
        $data['phone'] = $userName;
        $data['uniqueIdentifier'] = $inputData['machine_code'];
        $data['pwd'] = $inputData['password'];
        $id = $this->Account->insert($data);
        if($id >0 )
        {
            return json_encode(['code'=>1,'success'=>true,'msg'=>'注册成功']);
        }
        else{
            return json_encode(['code'=>0,'success'=>false,'msg'=>'注册失败']);
        }
    }

    public function  createRobot()
    {
        // return 8989;

        $start =10609 ; //7609   ;
        $end = 12609;// 10609  ;
        //$end = 7605  ;
        $nameList = $this->Aiinfo->select([], [], '*',5,6000 );
        $countName = count($nameList) ;
        $count = count($nameList);
        $nameIndex = 0;
        $ret = '';
        for ($i = $start ; $i< $end; $i++)
        {
            $nickName = $nameList[$nameIndex]['nickname'];
            $data = [
                'openid'=>$i,
                'usrid'=>$i,
                'nickname'=>$nickName,
                'headicon'=>'100'.rand(1,8),
                'ai'=>1,
                'phone'=>$i,
                'bankcard'=>$i,
                'chips'=>rand(100*1000,2000*1000),

            ];

            $id =  $this->Usr->insert($data);
            if($id >0 )
            {
                $ret .= '增加用户'.$i;
            }
            else{
                $ret .= '增加用户'.$i.'失败';
            }
            $nameIndex ++;
        }
        return $ret;

    }

    public function updateRobotName()
    {
        $usrList = $this->Usr->select(['ai'=>1], [], 'usrid,nickname');
        $countUser = count($usrList);
        //$end = 7605  ;
        $nameList = $this->Aiinfo->select([], [], '*',4,10000 );
        $countName = count($nameList) ;
        $nameIndex = 0;
        $ret = '';
        for ($i = 0 ; $i< $countUser; $i++)
        {
            $nickName = $nameList[$nameIndex]['nickname'];
            $this->Usr->update(['usrid'=>$usrList[$i]['usrid']],['nickname'=>$nickName]);
            $ret.='更新'.$usrList[$i]['usrid'].'昵称'.$nickName.'<br/>';
            $nameIndex++;
        }
        return $ret;
    }

    public function updateRobotChips()
    {
        $where['ai'] = 1;
        // $where['usrid'] = ['>'=>10609];
        $where['chips'] = ['<'=>2000*1000];
        $usrList = $this->Usr->select($where, [], 'usrid,nickname,chips');
        return json_encode($usrList);
        $countUser = count($usrList);
        $ret = '';
        for ($i = 0 ; $i< $countUser; $i++)
        {
            //$this->Usr->update(['usrid'=>$usrList[$i]['usrid']],['chips'=>rand(100*1000,2000*1000)]);
            $ret.='更新'.$usrList[$i]['usrid'].'余额<br/>';

        }
        return $ret;
    }



    public function createTestUser()
    {

        $createids ='';
        $idsStr = $this->request->param('ids');
        $password =  $this->request->param('pwd');
        $code=  $this->request->param('code');
        $output = '创建测试号';

        $adRes = $this->User->read_by_username($code);
        if (!isset($adRes['id'])) {
            return '推广号不存在';
        }
        if(!empty($idsStr))
        {
            $userList = explode(',',$idsStr);
            foreach ($userList as $item)
            {
                $userName = trim($item);
                if(strlen($userName)==11){
                    $tempRand = rand(1,9999999999);
                    $data['account'] = $item;
                    $data['times'] = date('Y-m-d H:i:s');
                    $data['password'] = md5($tempRand.$password.date("Y-m-d"));
                    $data['checktoken'] = $tempRand.'/'.$data['times'];
                    $data['ip'] = $this->request->get_client_ip() ;//$_SERVER["REMOTE_ADDR"];
                    $data['merchant_code'] = $adRes['username'];;
                    $data['phone'] = $userName;
                    $data['uniqueIdentifier'] = uniqid();
                    $id = $this->Account->insert($data);
                    if($id)
                    {
                        $output = '创建测试号:'.$userName.'成功密码'.$password.'<br/>';
                    }
                }
            }
        }

        return $output;

    }


}