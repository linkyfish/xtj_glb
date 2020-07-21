<?php

namespace Admin;

use Ctrl\AdminController;

// hook admin_menu_use.php
Class Menu extends AdminController
{

    // hook admin_menu_start.php

    /**
     * 组权限
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Role_GET()
    {
		if($this->token['RoleID'] != 1&&$this->token['RoleID'] != 2){
			$this->response('0001',[],'没权限');
		}
        $RoleID = $this->request->param('RoleID', 0);
        $Type = $this->request->param('Type', 0);
        // hook admin_menu_role_get_start.php

        if($Type==1){
            $role = $this->RoleNew->read(['RoleID'=>$RoleID]);
            $nodes = explode(',',$role['PowerList']);

            $menu = $this->NewMenu->select(['Status' => 1], [], 'Node as value,NodeName as title,Rank as rank');
            foreach ($menu as $k => $v) {
                in_array($menu[$k]['value'], $nodes, 1) AND $menu[$k]['checked'] = true;
                $menu[$k]['title'].=' - '.$v['value'];
            }

            $menu = arrlist_multisort($menu, 'rank', false);
        }else{

        }
        // hook admin_menu_role_get_end.php
        $this->response('0000', ['data' => $menu]);
    }

    // hook admin_menu_end.php

}

?>