<?php
/**
 * Created by PhpStorm.
 * User: yang
 * Date: 2019-11-17
 * Time: 11:58
 */

namespace Com;


use Ctrl\GameController;

class Settle_agent extends GameController {

    public function Index(){
        //获取当前任务列表 只显示最近的30条数据
        $taskList=$this->AgentMoneyTask->select([],["task_id"=>-1]);
        $adminInfos=$this->Admin->select([]);
        $mapAdminAccounts=arrlist_key_values($adminInfos,"ID","UserName");
        return $this->View(get_defined_vars());
    }
}