<?php
namespace Model;
// hook model_agent_day_stat_use.php

use App\Model;

class AgentDayStatModel extends Model
{
    // hook model_agent_day_stat_public_start.php
    public $table = 'agent_day_stat';
    //public $index = 'node';
    //public $is_delete = 'is_delete';

    // hook model_agent_day_stat_public_end.php

    // hook model_agent_day_stat_start.php

    public function find($AgentID,$Ymd,$reload=0){

    }

    // hook model_agent_day_stat_end.php
}

?>