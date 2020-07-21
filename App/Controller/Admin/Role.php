<?php

namespace Admin;

use Ctrl\AdminController;
// hook admin_role_use.php

Class Role extends AdminController
{
    // hook admin_role_start.php


    /**
     * Index_GET
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Index_GET()
    {
		if($this->token['RoleID'] != 1&&$this->token['RoleID'] != 2){
			$this->response('0001',[],'没权限');
		}
        $cond = [];
        $page = $this->request->param('page', 1);
        $size = $this->request->param('limit', 10);
        $field = $this->request->param('field', 'RoleID');
        $_order = $this->request->param('order', '');
        $role_name = $this->request->param('role_name', '');
        // hook admin_role_index_get_start.php

        empty($field) AND $field = 'RoleID';
        !empty($role_name) AND $cond['RoleName']['LIKE'] = $role_name;
        $order = [$field => $_order == 'asc' ? 1 : -1];
        $data = $this->RoleNew->GetList($cond, $order, $page, $size);
        // hook admin_role_index_get_end.php
        $this->response('0000', ['data' => $data]);
    }


    /**
     * Field_POST
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Field_POST()
    {
		if($this->token['RoleID'] != 1&&$this->token['RoleID'] != 2){
			$this->response('0001',[],'没权限');
		}
        $RoleID = $this->request->param('RoleID', 0);
        $field = $this->request->param('field', '');
        $value = $this->request->param('value', '');
        $this->CheckEmpty([$RoleID, $field], ['权限组ID', '修改字段']);
        $arr = ['RoleName','RoleDesc','IsEnable','dh','zc','jy','gm','fz','tr','ls','bj','yh','gn','sd','reg','czs','xgbz'];
        // hook admin_role_field_post_start.php

        !in_array($field, $arr) AND $this->response('0003', [], '字段不可修改');
        $role = $this->RoleNew->read(['RoleID'=>$RoleID]);
        empty($role['RoleID']) AND $this->response('0003', [], '账户不存在');
        switch ($field){
            case 'IsEnable':
            case 'RoleName':
            case 'RoleDesc':
                $this->token['RoleID']>$role['RoleID']  AND $this->response('0003', [], '权限等级不足');
                break;
        }
        // hook admin_role_field_post_start.php
        $this->RoleNew->update(['RoleID'=>$RoleID], [$field => $value]);
        $this->RoleNew->reload_role(1);
        $this->response('0000');
    }

    /**
     * 删除权限组
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function index_DELETE()
    {
		if($this->token['RoleID'] != 1&&$this->token['RoleID'] != 2){
			$this->response('0001',[],'没权限');
		}
        $RoleID = $this->request->param('RoleID', 0);
        // hook admin_role_index_delete_start.php
        $this->CheckEmpty([$RoleID], ['权限组']);
        $RoleID == 1 AND $this->response('0003', [], '超级管理员组不能删');
        $this->token['RoleID']>$RoleID  AND $this->response('0003', [], '权限等级不足');
        $this->RoleNew->delete(['RoleID'=>$RoleID]);
        $this->RoleAuth->delete(['RoleID'=>$RoleID]);
        // hook admin_role_index_delete_end.php
        $this->RoleNew->reload_role(1);
        $this->response('0000', '', '删除组成功');
    }

    /**
     * 修改权限组状态
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Status_POST()
    {
		if($this->token['RoleID'] != 1&&$this->token['RoleID'] != 2){
			$this->response('0001',[],'没权限');
		}
        $RoleID = $this->request->param('RoleID', 0);
        $status = $this->request->param('status', 0);
        // hook admin_role_status_post_start.php
        $this->CheckEmpty([$RoleID], ['权限组']);
        $RoleID == 1 AND $this->response('0003', [], '超级管理员组不能删');
        $this->RoleNew->update(['RoleID'=>$RoleID], ['IsEnable'=>$status]);
        $this->RoleNew->reload_role(1);
        // hook admin_role_status_post_end.php
        $this->response('0000', '', $status == 1 ? '启用成功' : '禁用成功');
    }

    /**
     * 获取菜单下拉框选项
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */

    public function Option_GET()
    {
		if($this->token['RoleID'] != 1&&$this->token['RoleID'] != 2){
			$this->response('0001',[],'没权限');
		}
        // hook admin_role_option_get_start.php
        $list = $this->RoleNew->select(['IsEnable' => 1], [], 'RoleID as RoleID,RoleName as role_name');
        // hook admin_role_option_get_end.php
        $this->response('0000', ['data' => $list]);
    }

    /**
     * Auth_POST
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Auth_POST()
    {
		if($this->token['RoleID'] != 1&&$this->token['RoleID'] != 2){
			$this->response('0001',[],'没权限');
		}
        $RoleID = $this->request->param('RoleID', 0);
        $Type = $this->request->param('Type', 0);
        $this->CheckEmpty([$RoleID], ['权限组']);
        $menus = $this->request->param('check', []);
        if($Type==1){
            $this->RoleNew->update(['RoleID'=>$RoleID],['PowerList'=>implode(',',$menus)]);
            $this->RoleNew->reload_role(1);
        }else{
            $this->RoleAuth->delete(['RoleID' => $RoleID]);
            $data = [];
            foreach ($menus as $menu) {
                $menu =explode("|",$menu);
                $data[] = [
                    'RoleID' => $RoleID, 'node' =>str_replace('/','_',$menu[0]),'module'=>$menu[1]
                ];
            }
            count($data) > 0 AND $this->RoleAuth->insertALL($data);
        }

        $this->response('0000', ['data'=>$menus]);
    }


    // hook admin_role_end.php
}

?>