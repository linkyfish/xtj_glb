<?php

namespace Com;

use Ctrl\GameController;

Class Admin_changerecharge extends GameController
{


    /*
    public function Index()
    {
        $this->needadmin(1);

        $payconfig = $this->PayConfig->select();
        $pay = $pay10 = $pay20 = $pay30 = $pay40 = $pay50 = [[0=>'未选择']];
        foreach ($payconfig as $k=> $v) {
            $pay[$v['ID']] = $v['Name'];
            if ($v['Channel10']) {
                $pay10[$v['ID']] = $v['Name'];
            }
            if ($v['Channel20']) {
                $pay20[$v['ID']] = $v['Name'];
            }
            if ($v['Channel30']) {
                $pay30[$v['ID']] = $v['Name'];
            }
            if ($v['Channel40']) {
                $pay40[$v['ID']] = $v['Name'];
            }
            if ($v['Channel50']) {
                $pay50[$v['ID']] = $v['Name'];
            }

            unset($payconfig[$k]['AppID'],$payconfig[$k]['AppKey'],$payconfig[$k]['AppSess'],$payconfig[$k]['AppUrl']);
        }

        $payconfig = arrlist_change_key($payconfig,'ID');
        return $this->View(get_defined_vars());
    }*/


    public function Index()
    {
        $this->needadmin(1);
        $payconfig = $this->PayConfig->select();
        return $this->View(get_defined_vars(),'Admin_changerecharge.channellist');
    }

}
