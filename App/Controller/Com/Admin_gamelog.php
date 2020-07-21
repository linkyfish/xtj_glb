<?php

namespace Com;

use Ctrl\GameController;

Class Admin_gamelog extends GameController
{

	public function Index()
	{
		$this->needadmin(1);

		return $this->View();
	}
}
