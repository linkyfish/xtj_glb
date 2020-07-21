<?php

namespace Com;

use Ctrl\GameController;

Class Redis extends GameController
{

	public function Index()
	{
		$this->needadmin(1);
		$info = $this->User->CacheInfo();
		return $this->View(get_defined_vars());
	}

	public function Keys(){
		$this->needadmin(1);
		$keys = str_replace('db','', $this->request->param('keys','db0'));
		$filter=$this->request->param('filter','*');
		$page=$this->request->param('page',1);
		$limit=$this->request->param('limit',50);
		empty($filter) && $filter='*';
		$db=$this->User->cache->db;
		$this->User->CacheSelect($keys);
		//$size=$this->User->cache->dbSize();
		$keys = $this->User->cache->keys($filter);
		sort($keys);
		$array = array_slice($keys,($page-1)*$limit,$limit);
		$this->User->CacheSelect($db);
		$results=[];
		foreach ($array as $v){
			$results[]=['keys'=>$v];
		}

		$data=[
			'total'=>count($keys),
			'results'=>$results
		];

		$this->response('0000',$data);
	}


	public function Delete(){
		$this->needadmin(1);
		$dbs = str_replace('db','', $this->request->param('db','db0'));
		$keys=explode(',',$this->request->param('keys'));
		empty($keys)&& $this->response('0001',[],'没有指定key');
		$db=$this->User->cache->db;
		$this->User->CacheSelect($dbs);
		$this->User->cache->del($keys);
		$this->User->CacheSelect($db);
		$this->response('0000');
	}
	public function Viewc(){
		$this->needadmin(1);
		$dbs = str_replace('db','', $this->request->param('db','db0'));
		$keys= $this->request->param('keys');
		empty($keys)&& $this->response('0001',[],'没有指定key');
		$db=$this->User->cache->db;
		$this->User->CacheSelect($dbs);
		$len = mb_strlen($this->User->cache->cachepre);
		$data=$this->User->CacheGet(substr($keys,$len));
		$this->User->CacheSelect($db);
		$this->response('0000',['data'=>xn_json_encode($data)]);
	}

}
