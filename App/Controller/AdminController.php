<?php

namespace Ctrl;

// hook admincontroller_use.php


Class AdminController extends Controller
{
    public $rolearray = [];
    public function __construct($server, $route, \Request $request, $response)
    {
        parent::__construct($server, $route, $request, $response);
        $this->session_start();
        $this->UserInfo('token_uid', '../../');
        $this->RoleNew->reload_role();
        $this->rolearray = arrlist_change_key($this->RoleNew->role, 'RoleID');
    }

}

?>