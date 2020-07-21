<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019-11-17
 * Time: 11:58
 */

namespace Com;


use Ctrl\GameController;

class Settle_detail extends GameController {

    public function Index(){
        //获取当前任务列表 只显示最近的30条数据
        $id=$this->request->param("id","");
        if(empty($id)){
            $this->response("0001",[],"参数错误");
        }
        $agentCurrent=$this->AgentMoneyList->find_one(["id"=>$id]);
        $needDo=explode(",",$agentCurrent["children_ids"]);

        $agentDetails[]=$this->AgentMoneyDetail->find_one([
            "task_id"=>$agentCurrent["task_id"],
            "agent_id"=>$needDo
        ]);
        $tmp=$this->AgentMoneyDetailChildren->select(["detail_id"=>$agentDetails[0]["detail_id"]]);

        $agentDetails=array_merge($agentDetails,$tmp);

        $agentAll=$this->Agent->select([]);
        $mapAgentBasicInfo=arrlist_key_item($agentAll,"AgentID");


        return $this->View(get_defined_vars());
    }
}