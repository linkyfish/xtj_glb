<?php

namespace J;

use Ctrl\Controller;

Class Index extends Controller
{
	public function Index(){
    	$this->response('0000',[],'','http://'.createCode(5),302);
	}

}
