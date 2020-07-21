<?php
namespace Model;
// hook model_role_new_use.php
use App\Model;

class RoleNewModel extends Model
{
    // hook model_role_new_public_start.php
    public $table = 'role_new';
    public $index = 'RoleID';
    public $role_kv=[];
    public $role=[];
    //public $is_delete = 'is_delete';
    // hook model_role_new_public_end.php

    public function __construct($app) {
        parent::__construct($app);
        $this->reload_role(1);
    }

    // hook model_role_new_start.php

    public function reload_role($reload=0){
        $cache = $this->CacheGet($this->table);
        if(!$cache||$reload==1){
            $cache = $this->select(['IsEnable'=>1]);
            $this->CacheSet($this->table,$cache);
        }
        $this->role_kv= arrlist_key_values($cache,'RoleID','RoleName');
        $this->role=$cache;
        return $cache;
    }

    public function setting($RoleID,$setting){
        $this->reload_role(1);
    }
    // hook model_role_new_end.php
}

?>