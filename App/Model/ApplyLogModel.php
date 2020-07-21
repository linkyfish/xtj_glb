<?php
namespace Model;

class ApplyLogModel extends \App\Model {
    //public $link='db1';
    public $table = 'apply_log';
    public $index ='LogID';
    public $Status=[
    	0=>'待审核',1=>'通过',2=>'忽略'
	];

}