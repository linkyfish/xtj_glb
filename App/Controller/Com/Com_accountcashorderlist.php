<?php

namespace Com;

use Ctrl\GameController;

Class Com_accountcashorderlist extends GameController {

    public function Index () {
		$repay_config = $this->RepayConfig->select();
        return $this->View(get_defined_vars());
    }
}
