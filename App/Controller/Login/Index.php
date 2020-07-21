<?php

namespace Login;

use Ctrl\Controller;

Class Index extends Controller {

    // hook index_index_start.php

    public function Index()
    {
        $isagent =   $this->request->_S("AGENT");
        return $this->View(get_defined_vars());
    }

    public function Index_safecodetips()
    {
        return $this->View();
    }
}
