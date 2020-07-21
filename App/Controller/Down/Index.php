<?php

namespace Down;

use Ctrl\Controller;

Class Index extends Controller
{

    public function Index()
    {
        $app_size=  _CONF('app_size');
        $app_version= _CONF('app_version');
        $app_name=  _CONF('app_name');
        $mainurl=  _CONF('mainurl');
        $mobileprovision =  _CONF('mobileprovision');
        $apk_url=  '#';
        $ios_url =  '#';
        $dowaloadOpen = _CONF('downloadPageOpen');
        if($dowaloadOpen==1)
        {
            $apk_url=  _CONF('apk_down_url');
            $ios_url =  _CONF('ios_down_url');
            return $this->View(get_defined_vars(),'Index.Index');
        }
        else{
            //return $this->View('','Index.update');
            return $this->View('','Index.update');
        }
    }


    public function Update()
    {

        return $this->View(get_defined_vars());
    }


    public function demodownload()
    {

        $app_size=  _CONF('app_size');
        $app_version= _CONF('app_version');
        $app_name=  _CONF('app_name');
        $mainurl=  _CONF('mainurl');
        $mobileprovision =  _CONF('mobileprovision');
        $apk_url=  '#';
        $ios_url =  '#';
        $dowaloadOpen = _CONF('downloadPageOpen');
        if($dowaloadOpen==1)
        {
            $apk_url=  _CONF('apk_down_url');
            $ios_url =  _CONF('ios_down_url');
            return $this->View(get_defined_vars(),'Index.demodownload');
        }
        else{
            //return $this->View('','Index.update');
            return $this->View('','Index.demodownload');
        }
    }


}