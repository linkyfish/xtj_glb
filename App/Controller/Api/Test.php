<?php


namespace Api;
use Ctrl\Controller;
use Ctrl\GameController;
use Swoole\Mysql\Exception;


class Test  extends  Controller
{


    public  function fixreguser()
    {
        $list = $this->User->select([],['id'=>-1],'id,parent_id,guid,username');
        $listGroup = arrlist_group($list,'parent_id');

        foreach ( $listGroup as $id => $row)
        {
            if($id>0 )
            {
                $this->User->update(['id'=>$id],['Reg_User'=>count($row)]);
            }
        }
    }
    public function  index()
    {

        $time = time();
        $timeCut = substr($time,strlen($time)-5,5) ;
        return $time.'-'.$timeCut;
        //api_salt => oiea932na
        //$sign = md5($formData['gameid'].$formData['bank']._CONF('api_salt').$timeCut);

        /*
        $sDate = $this->request->param('sDate', date('Y-m-01'));
        $eDate = $this->request->param('eDate', date('Y-m-d'));
        $start = $sDate . ' 00:00:00';
        $end = $eDate . ' 23:59:59';
        $agent = $this->default_user;
        $agent_id = $agent['id'];
        $agent_son = [];
        $ymd = $this->MoneyTrans->ymd_array($start, $end);
        $ymd_kv = $this->MoneyTrans->ymd_array($start, $end, 'agent_');
        $agent_kv = $this->Kv->select($ymd_kv);
        $agent_son = [];

        foreach ($agent_kv as $k => $v) {
            $son = [];
            $this->MoneyTrans->agent_child_list($agent_id, $this->MoneyTrans->make_son($v), $son);
            $son = arrlist_values($son, 'id');
            $son[] = $agent_id;
            $agent_son[str_replace('agent_', '', $k)] = $son;
        }

        return json_encode($agent_son);

        */
        



    }
    public function index2()
    {
        //$data['start'] = time()-86400*3;
        $start = time();
        $ymd = date('Ymd', $start);
        $usrmoneyList = $this->UsrAgentMoney->select(['ymd' => $ymd,'status'=>0]);
        $usrmoney= arrlist_key_values($usrmoneyList,'usrid','ymd');
        $usrmoneyParent = arrlist_key_values($usrmoneyList, 'usrid', 'agent_id');
        $usrmoneyGroup = arrlist_group($usrmoneyList,'usrid' );
        $userTime = time()-7200;
        $condNewuser['a.online'] = ['>'=>0];
        $condNewuser['b.Last_Reg_User'] = 0;

        $list= $this->Usr->GetWithList([['table' => $this->User->table . ' b', 'join' => 'left', 'and' => 'b.guid = a.usrid']], $condNewuser, ['a.offline_time' => -1], 1, 10000000,'a.usrid,a.agent_id,b.Last_Reg_User,b.parent_id,b.guid,b.Game_Bet,b.Poker_Bet');
        $users = $list['results'];
        $arr = [];
        $insertArr =[];
        $upateArr = [];
        foreach ($users as $item)
        {
            //流水达到200奖励上级
            $Game_Bet = arrlist_sum($usrmoneyGroup[$item['usrid']],'Game_Bet');
            $Poker_Bet = arrlist_sum($usrmoneyGroup[$item['usrid']],'Poker_Bet');
            $totalBet = $Game_Bet+$Poker_Bet +$item['Game_Bet'] +$item['Poker_Bet'];
            $parent_id = 0;
            $a =[
                'userid'=>$item['usrid'],
                'Game_Bet'=>$Game_Bet,
                'Poker_Bet'=>$Poker_Bet,
                'totalBet'=>$totalBet,
            ];

            if(!empty($usrmoneyParent[$item['usrid']]))
            {
                $a['parent_id'] = $parent_id;
                $parent_id = $usrmoneyParent[$item['usrid']];;
            }

            if($totalBet>=500000)
            {
                if($parent_id>0)
                {
                    if(empty($usrmoney[$parent_id]))
                    {

                        $parentInfo = $this->User->read(['guid'=>$parent_id]);
                        if($parentInfo)
                        {
                            $parentParentId = $parentInfo['parent_id'];
                            if($parentParentId>0)
                            {
                                $parentParentInfo= $this->User->read(['id'=>$parentParentId]);
                                if($parentParentInfo)
                                {
                                    $insert['ymd'] = $ymd;
                                    $insert['usrid'] = $parent_id;
                                    if(!empty($parentParentInfo['guid']))
                                    {
                                        $insert['agent_id'] = $parentParentInfo['guid'];
                                    }
                                    $insert['created_at'] = time();
                                    $insert['update_at'] = time();
                                    $insert['Friend_Bet_Reward'] = 30000;
                                    //$this->UsrAgentMoney->insert($insert);
                                    //$this->User->update(['guid'=>$item['usrid']],['Log_Reward_Status'=>1]);
                                    $insertArr[] = $insert;
                                }
                            }
                        }
                    }
                    else{
                        $update['Friend_Bet_Reward+'] = 30000;
                        $update['update_at'] = time();
                        //$this->UsrAgentMoney->update(['usrid'=>$parent_id,'ymd'=>$ymd],$update);
                        //$this->User->update(['guid'=>$item['usrid']],['Log_Reward_Status'=>1]);
                        $upateArr[] = array_merge($update,['usrid'=>$parent_id,'ymd'=>$ymd]) ;
                    }
                }


            }


            $arr[] = $a;
        }

        return json_encode(['a'=>$a,'insert'=>$insertArr,'update'=>$users]);
    }
    public function index22222()
    {

        //$data['start'] = time()-86400*3;
        $start = time();
        $ymd = date('Ymd', $start);
        $usrmoneyList = $this->UsrAgentMoney->select(['ymd' => $ymd,'status'=>0]);
        $usrmoney= arrlist_key_values($usrmoneyList,'usrid','ymd');
        $usrmoneyParent = arrlist_key_values($usrmoneyList, 'usrid', 'agent_id');
        $usrmoneyGroup = arrlist_group($usrmoneyList,'usrid' );
       // $condNewuser['a.online'] = ['>'=>0];
        $condNewuser['b.Log_Reward_Status'] = 0;


        $todayBetUserIds = arrlist_values($usrmoneyList,'usrid');


        $list= $this->Usr->GetWithList([['table' => $this->User->table . ' b', 'join' => 'left', 'and' => 'b.guid = a.usrid']], $condNewuser, ['a.offline_time' => -1], 1, 10000000,'a.usrid,a.agent_id,b.Log_Reward_Status,b.parent_id,b.guid,b.Game_Bet,b.Poker_Bet');
        $users = $list['results'];
        $arr = [];
        $insertArr =[];
        $upateArr = [];
        foreach ($users as $item)
        {
            if(in_array($item['usrid'],$todayBetUserIds))
            {
                //流水达到200奖励上级
                $Game_Bet = arrlist_sum($usrmoneyGroup[$item['usrid']],'Game_Bet');
                $Poker_Bet = arrlist_sum($usrmoneyGroup[$item['usrid']],'Poker_Bet');
                $totalBet = $Game_Bet+$Poker_Bet +$item['Game_Bet'] +$item['Poker_Bet'];
                $parent_id = 0;
                $a =[
                    'userid'=>$item['usrid'],
                    'Game_Bet'=>$Game_Bet,
                    'Poker_Bet'=>$Poker_Bet,
                    'totalBet'=>$totalBet,
                ];

                if(!empty($usrmoneyParent[$item['usrid']]))
                {
                    $a['parent_id'] = $parent_id;
                    $parent_id = $usrmoneyParent[$item['usrid']];;
                }

                if($totalBet>=200000)
                {
                    if($parent_id>0)
                    {
                        if(empty($usrmoney[$parent_id]))
                        {

                            $parentInfo = $this->User->read(['guid'=>$parent_id]);
                            if($parentInfo)
                            {
                                $parentParentId = $parentInfo['parent_id'];
                                if($parentParentId>0)
                                {
                                    $parentParentInfo= $this->User->read(['id'=>$parentParentId]);
                                    if($parentParentInfo)
                                    {
                                        $insert['ymd'] = $ymd;
                                        $insert['usrid'] = $parent_id;
                                        if(!empty($parentParentInfo['guid']))
                                        {
                                            $insert['agent_id'] = $parentParentInfo['guid'];
                                        }
                                        $insert['created_at'] = time();
                                        $insert['update_at'] = time();
                                        $insert['Reg_Reward'] = 30000;
                                        $this->UsrAgentMoney->insert($insert);
                                        $this->User->update(['guid'=>$item['usrid']],['Log_Reward_Status'=>1]);
                                        $insertArr[] = $insert;
                                    }
                                }


                            }

                        }
                        else{
                            $update['Reg_Reward+'] = 30000;
                            $update['update_at'] = time();
                            $this->UsrAgentMoney->update(['usrid'=>$parent_id,'ymd'=>$ymd],$update);
                            $this->User->update(['guid'=>$item['usrid']],['Log_Reward_Status'=>1]);
                            $upateArr[] = array_merge($update,['usrid'=>$parent_id,'ymd'=>$ymd],['guid'=>$item['usrid']]) ;

                        }
                    }
                }

                $arr[] = $a;
            }

        }

        return json_encode(['a'=>$a,'insert'=>$insertArr,'update'=>$upateArr,'usrmoneyGroup'=>$users]);
    }


    public function fen()
    {

        //类型（0游戏 1免费领取 2绑定账号 3任务领取 4连续登陆 5救济金 6升级奖励 25=>'注册送', 29=>'申请送', 35=>'系统送', 36=>'活动送', 37=>'提现', 38=>'取消提现', 39=>'禁用提现', 50=>'短信送', 51=>'手工上分', 52=>'手工减分', 61=>'商城上分',
        $type = $this->request->param('type',1);

        $chongzhi['Type'] = ['51','61','38','25','29','50'];
        $chongzhi['AddTime'] = ['>='=>1585152000,'<='=>1586275199];
        $tixian['Type'] = ['37'];
        $tixian['AddTime'] = ['>='=>1585152000,'<='=>1586275199];

        $listChongzhi = $this->Gamebilog->select($chongzhi, ['Money'=>-1], 'AdminID,SUM(Money) as Money,UsrID', 1, 5000000, '', 'GROUP BY UsrID');
        $listTixian = $this->Gamebilog->select($tixian, ['Money'=>-1], 'AdminID,SUM(Money) as Money,UsrID', 1, 5000000, '', 'GROUP BY UsrID');
        $userList = $this->Usr->select();
        $agentList = $this->User->select();
        $output = '<table>';
        $output.= "<tr><td>用户账号</td> <td>用户id</td><td>充值</td><td>提现</td><td>上次登录IP</td><td>推广号</td></tr>";

        foreach ($listChongzhi as $item)
        {

            $tixian = 0;

            foreach ($listTixian as  $itemTixian )
            {
                if($item['UsrID'] == $itemTixian['UsrID']  )
                {
                    $tixian = $itemTixian['Money'];
                }
            }
            foreach ($userList as $uItem)
            {
                 if($uItem['usrid'] == $item['UsrID'])
                 {
                     $phone = $uItem['openid'];
                     if($uItem['agent_id']==1407)
                     {
                         foreach ($agentList as  $agentTixian )
                         {
                             $agent = '';
                             if($agentTixian['id'] == $uItem['agent_id'])
                             {
                                 $output.= sprintf( "<tr> <td>%s</td> <td>%s</td><td>%s</td><td>%s</td><td>%s</td><td>%s</td></tr>",$phone,$item['UsrID'],$item['Money']/1000,$tixian/1000,$uItem['last_login_ip'],$agentTixian['username']);
                             }

                         }
                     }

                 }
            }



        }
        $output .= '</table>';
        return $output;
    }

    public function  getIpNameList()
    {
        $LatestLoginIP = $this->UserStat->CacheGet('LatestLoginIPKey_');
        return  json_encode($LatestLoginIP);

    }

    public function  getSameNameCashList()
    {
        $LatestLoginIP = $this->UserStat->CacheGet('SameNameCash_');
        return  json_encode($LatestLoginIP);

    }
    public function  getFenUrl()
    {
        //$host = '123.nzxhne.cn';
        //http://ehif.hslm6666.cn//index.php/home/index/apir
        $host = 'ehif.hslm6666.cn';
        $cli = new \Swoole\Coroutine\Http\Client($host, 80);
        $cli->setHeaders([
            'Host' => $host,
            'Content-Type' => 'application/x-www-form-urlencoded',
        ]);
        $cli->set(['timeout' => 60]);
        $data['title'] = '大洋娱港';
        $url = '/?code=';
        $data['url'] = $url;
        $cli->post('/index.php/home/index/apir', $data);
        $resultt = htmlspecialchars($cli->body);
        $diewe = strstr($resultt, 'ttps://');
        $cv = 'h' . $diewe;//$cv就是你的短连接
        $content =  $cli->body;
        $cli->close();
        return $content;
    }

    public function  getFengkongUserList()
    {
        $OnlineUserMoneyInfoKey = 'OnlineUserMoneyInfo_';
        $OnlineUserMoneyInfo =   $this->UserStat->CacheGet($OnlineUserMoneyInfoKey);
        return json_encode($OnlineUserMoneyInfo);
    }


    public function  getPhoneKey()
    {
        $phone = $this->request->get('phone');
        $key = 'sms_'.$phone;
        return  json_encode($this->UserStat->CacheGet($key));
    }

}