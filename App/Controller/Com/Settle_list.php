<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019-11-17
 * Time: 11:58
 */

namespace Com;


use Ctrl\GameController;

class Settle_list extends GameController {

    public function Index(){
        //获取当前任务列表 只显示最近的30条数据
        $taskId=$this->request->param("task_id","");
        if(empty($taskId)){
            $this->response("0001",[],"参数错误");
        }
        $agentMoneyList=$this->AgentMoneyList->select(["task_id"=>$taskId],["real_money"=>-1]);
        $zeroList=[];
        if(!empty($agentMoneyList)){
            foreach ($agentMoneyList as $index => $item) {
                if($item["real_money"]==0){
                    $zeroList[]=$item;
                    unset($agentMoneyList[$index]);
                }
            }
        }
        $agentMoneyList=array_merge($agentMoneyList,$zeroList);

        $taskInfo=$this->AgentMoneyTask->find_one(["task_id"=>$taskId]);
        $adminInfos=$this->Admin->select([]);
        $agentAll=$this->Agent->select([]);
        //累计欠款
        $mapLess=arrlist_key_values($agentAll,"AgentID","less_money");
        $mapAgentBasicInfo=arrlist_key_item($agentAll,"AgentID");
        $mapAdminAccounts=arrlist_key_values($adminInfos,"ID","UserName");
        return $this->View(get_defined_vars());
    }
}