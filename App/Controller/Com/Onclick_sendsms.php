<?php

namespace Com;

use Ctrl\GameController;

Class Onclick_sendsms extends GameController {

    public function Index () {
        $sid = $this->request->param('sid','');
        empty($sid) AND $this->response('0001',[],'参数不足.');
        $player = $this->findPlayer($sid);
        $send_sms = _CONF('SmsLargess');
        $send_sms = explode(',',$send_sms);
        return $this->View(get_defined_vars());
    }
}
