<?php

namespace Api;
use Co;
use Ctrl\Controller;
Class Payment extends  Controller
{

    public function getList()
    {
        $list = $this->PayConfig->select();
        $showResult = null;
        foreach ($list as $item)
        {
            if($item['Channel10'] ==1)
            {
                $showResult['Channel10'][] = [
                    'name'=>$item['Name'],
                    'id'=>$item['ID'],
                    'prices'=>$item['Price10'],
                ];
            }
            if($item['Channel20'] ==1)
            {
                $showResult['Channel20'][] = [
                    'name'=>$item['Name'],
                    'id'=>$item['ID'],
                    'prices'=>$item['Price20'],
                ];
            }
            if($item['Channel30'] ==1)
            {
                $showResult['Channel30'][] = [
                    'name'=>$item['Name'],
                    'id'=>$item['ID'],
                    'prices'=>$item['Price30'],
                ];
            }
            if($item['Channel40'] ==1)
            {
                $showResult['Channel40'][] = [
                    'name'=>$item['Name'],
                    'id'=>$item['ID'],
                    'prices'=>$item['Price40'],
                ];
            }
            if($item['Channel50'] ==1)
            {
                $showResult['Channel50'][] = [
                    'name'=>$item['Name'],
                    'id'=>$item['ID'],
                    'prices'=>$item['Price50'],
                ];
            }
        }
     return  json_encode(['code'=>0,'success'=>true,'payments'=>$showResult]);

    }
}