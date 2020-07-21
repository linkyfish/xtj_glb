<?php

namespace Com;

use Ctrl\GameController;

Class  Admin_addmoney extends GameController
{

	public function Index()
	{
		$this->needadmin();
		$_roles=$this->admin_role_arr;
		unset($_roles[0],$_roles[1]);
		$_accs = $this->User->select(['RoleID'=>array_values($_roles)]);
		return $this->View(get_defined_vars());
	}
}
