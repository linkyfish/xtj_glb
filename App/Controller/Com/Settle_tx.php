<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019-11-20
 * Time: 02:29
 */

namespace Com;


use Ctrl\GameController;

class Settle_tx  extends GameController
{

    public function Index(){
            $agentInfo=$this->agent_info;
        $agentInfo=$this->Agent->find_one(['AgentID'=>$agentInfo["AgentID"]]);
        $zhangdanList=$this->AgentMoneyList->GetList(["agent_id"=>$agentInfo["AgentID"],"status"=>1],["id"=>-1],1,30);
        $userInfo=$this->TAccount->find_one(["ID"=>$agentInfo["UserID"]]);
        return $this->View(get_defined_vars());
        }
}