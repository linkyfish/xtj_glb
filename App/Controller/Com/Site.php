<?php

namespace Com;

use Ctrl\GameController;
use Server\Bt;

Class Site  extends GameController
{

    public function Index()
    {
        $bt = $this->getBt();
        $types = $bt->Webtypes();

        $showList = [];
        foreach ($types as $type)
        {
            if($type['id'] !=0)
            {
                $result = $bt->Websites('',1,2500,$type['id']);
                $showList[]  = [
                    'list'=>$result['data'],
                    'name'=>$type['name'],
                    'id'=>$type['id'],
                ];
            }
        }
        return $this->View(get_defined_vars());
    }

    private  function  getBt()
    {
        $bt = new Bt(_CONF('WebSiteBtApiUrl'),_CONF('WebSiteBtApiToken'));
        return $bt;
    }

    public function Index_WebSiteList()
    {

    }


}