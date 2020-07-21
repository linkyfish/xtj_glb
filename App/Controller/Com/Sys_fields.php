<?php

namespace Com;

use Ctrl\GameController;

// hook com_sys_fields_use.php

Class Sys_fields extends GameController
{
    // hook com_sys_fields_start.php

    /**
     * Index_Put
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Index()
    {
        // hook com_sys_fields_index_put_use.php
        return $this->View();
    }

    /**
     * Index_GET
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Index_data()
    {
        // hook com_sys_fields_index_get_start.php
        $dblink = $this->request->param('dblink',$_ENV['conf']['db']['database_name']);
        $data = $this->TableField->show_tables($dblink);
        foreach ($data as &$row){
            $row['Data_length']=humansize($row['Data_length']);
            $row['Index_length']=humansize($row['Index_length']);
        }
        // hook com_sys_fields_index_get_end.php
        $this->response('0000', ['data' => $data]);
    }


    public function Index_detail(){
        // hook com_sys_fields_detail_get_start.php
        $dblink = $this->request->param('dblink',$_ENV['conf']['db']['database_name']);
        $table = $this->request->param('table','');

        $data = $this->TableField->show_columns($table);

        // hook com_sys_fields_detail_get_end.php
        $this->response('0000', ['data' => $data]);
    }


    // hook com_sys_fields_end.php
}

?>