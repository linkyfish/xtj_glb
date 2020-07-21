<?php

namespace Server\Libs;

/**
 * Class Model
 * @package Server\Libs
 *  @property \Server\App $app
 *  @property \Db $db
 *  @property \Cache $cache
 *  @property \Server\Server $server
 *  @property \swoole_websocket_server $http_server
//IDE_LOAD_START

//IDE_LOAD_END
 */
class Model
{

    public $db;
    public $app;
    public $cache;
    public $server;
    public $http_server;

    public $link = 'db';
    public $is_delete='';

    public function __construct($server)
    {

        $this->server = $server;
        $this->http_server = $server->http_server;
        $this->db = $_ENV['db_class'][$this->link];
        $this->cache = $_ENV['cache_class'];
    }

    /**
     * User: zhixiang
     *  Explain:
     *  -
     *
     * @param $name
     *
     * @return mixed
     */
    public function __get($name)
    {
        if (isset($_ENV['_models'][$name])) {
            return $this->$name = $_ENV['_models'][$name];
        }elseif(class_exists("\\Model\\{$name}Model",false)){
            $model = "\\Model\\{$name}Model";
            $_ENV['_models'][$name] = new $model($this->server);
            return $this->$name = $_ENV['_models'][$name];
        } else {
           throw new \Exception($name.'Model禁止访问', '500');
        }
    }

    public function CacheSet($k, $v, $life = 0)
    {
        return $this->cache->set($k, $v, $life);
    }

    public function CacheGet($k)
    {
        return $this->cache->get($k);
    }
    public function CacheFlushAll()
    {
        return $this->cache->flushall();
    }

    public function CacheDel($k)
    {
        return $this->cache->delete($k);
    }

    public function CacheSelect($id)
    {
        return $this->cache->select($id);
    }

    public function CacheLlen($id)
    {
        return $this->cache->llen($id);
    }

    public function CacheLpop($id)
    {
        return $this->cache->lpop($id);
    }

    public function CacheRpush($k, $data)
    {
        return $this->cache->rpush($k, $data);
    }

    public function CacheLpush($k, $data)
    {
        return $this->cache->lpush($k, $data);
    }

    public function CacheSmembers($id)
    {
        return $this->cache->smembers($id);
    }

    public function CacheTruncate()
    {
        return $this->cache->truncate();
    }

    public function CacheInfo()
    {
        return $this->cache->info();
    }

    public function show_tables($name)
    {
        return $this->db->sql_find('SHOW TABLE STATUS FROM ' . $name);
    }
    public function show_columns($name)
    {
        return $this->db->sql_find('SHOW FULL COLUMNS FROM ' . $name);
    }

    public function query($sql)
    {
        return $this->db->sql_find($sql);
    }

    public function find_one($cond, $order = [], $select = '*')
    {
        !empty($this->is_delete) && !isset($cond[$this->is_delete]) AND $cond[$this->is_delete]=0;
        return $this->db->find_one($this->table, $cond, $order, $select);
    }

    public function count($cond=[], $select = '*')
    {
        !empty($this->is_delete) && !isset($cond[$this->is_delete]) AND $cond[$this->is_delete]=0;
        return $this->db->count($this->table, $cond, $select);
    }

    public function sum($sum, $cond)
    {
        !empty($this->is_delete) && !isset($cond[$this->is_delete]) AND $cond[$this->is_delete]=0;
        return $this->db->sum($this->table, $sum, $cond);
    }
    public function Max($sum, $cond)
    {
        //!empty($this->is_delete) && !isset($cond[$this->is_delete]) AND $cond[$this->is_delete]=0;
        return $this->db->Max($this->table, $sum, $cond);
    }

    public function update($cond, $data)
    {
        !empty($this->is_delete) && !isset($cond[$this->is_delete]) AND $cond[$this->is_delete]=0;
        return $this->db->update($this->table, $cond, $data);
    }

    public function select($cond = [], $order = [], $select = '*', $page = 0, $limit = 0, $key = '', $group = '')
    {
        if (is_string($select) && $select != '*') {
            $select = explode(',', $select);
        }
        !empty($this->is_delete) && !isset($cond[$this->is_delete]) AND $cond[$this->is_delete]=0;
        return $this->db->find($this->table, $cond, $order, $page, $limit, $key, $select, $group);
    }

    public function delete($cond)
    {
        if(!empty($this->is_delete)){
            return $this->db->update($this->table, $cond,[$this->is_delete=>1]);
        }else{
            return $this->db->delete($this->table, $cond);
        }

    }

    public function insert($data)
    {
        return $this->db->insert($this->table, $data);
    }


    /**
     * User: zhixiang
     *  Explain:
     *  - 批量插入数据
     *
     * @param      $table 表名
     * @param      $arr   array('字段1'=>数据1,'字段2'=>数据2)
     * @param null $d
     *
     * @return bool|int|mixed|resource
     */

    public function insertALL($data)
    {
        return $this->db->insert_batch($this->table, $data);
    }

    public function read($cond, $select = '*')
    {
        return $this->find_one($cond, [], $select);
    }



    public function GetList($where, $order = [], $page = 1, $limit = 10, $select = '*', $ispage = 1, $group = '')
    {
        !empty($this->is_delete) && !isset($where[$this->is_delete]) AND $where[$this->is_delete]=0;

        if (!empty($this->index) && empty($group)) {
            $index_list = $this->select($where, $order, $this->index, $page, $limit, $this->index, '');
            $index_list_index = array_keys($index_list);
            $_list = !empty($index_list_index) ? $this->select([$this->index => $index_list_index], [], $select, 0, 0, $this->index) : [];
            $list = [];
            foreach ($index_list as $k => $v) {
                !empty($_list[$k]) AND $list[] = $_list[$k];
            }
            unset($index_list, $index_list_index, $_list);
        } else {
            $list = $this->select($where, $order, $select, $page, $limit, '', $group);
        }

        if ($ispage == 1) {
            $result['page'] = max(1, $page);
            $result['rows'] = $limit;
            $result['total'] = $this->count($where, !empty($this->index) ? $this->index : 1);
            $result['results'] = $list;
            $result['next_page'] = ceil($result['total'] / $limit) > $result['page'];
        } else {
            $result = $list;
        }
        return $result;
    }

    public function GetListAll($where, $order = '', $page = 1, $limit = 10, $select = '*', $ispage = 1, $group = '')
    {
        !empty($this->is_delete) && !isset($where[$this->is_delete]) AND $where[$this->is_delete]=0;

        if (!empty($this->index) && empty($group)) {
            $index_list = $this->select($where, $order, $this->index, $page, $limit, $this->index, $group);
            $index_list_index = array_keys($index_list);
            $_list = $this->select([$this->index => $index_list_index], [], $select, 0, 0, $this->index);
            $list = [];
            foreach ($index_list as $k => $v) {
                !empty($_list[$k]) AND $list[] = $_list[$k];
            }
            unset($index_list, $index_list_index, $_list);
        } else {
            $list = $this->select($where, $order, $select, $page, $limit, '', $group);
        }

        if ($ispage == 1) {
            $result['page'] = max(1, $page);
            $result['rows'] = $limit;
            $result['total'] = $this->count($where, !empty($this->index) ? $this->index : 1);
            $result['results'] = $list;
            $result['next_page'] = ceil($result['total'] / $limit) > $result['page'];
        } else {
            $result = $list;
        }
        return $result;
    }

    /**
     * User: zhixiang
     *  Explain:
     *  -
     *
     * @param array $table [ ['table'=>'user b','join'=>'left','and'=>'a.uid=b.uid']  ]
     * @param array $order
     * @param int $page
     * @param int $limit
     * @param string $select
     * @param int $ispage
     *
     * @return array|bool
     */
    public function GetWithList($join = [], $cond = [], $order = [], $page = 1, $limit = 10, $select = '*', $ispage = 1, $group = '')
    {
        $str = [];
        foreach ($join as $v) {
            $str[] = (empty($v['join']) ? 'left' : $v['join']) . ' join ' . $v['table'] . ' on ' . $v['and'];
        }
        if ($ispage == 1) {
            $result['page'] = max(1, $page);
            $result['rows'] = $limit;
            $result['total'] = $this->db->count($this->table . ' a ' . implode(' ', $str), $cond, !empty($this->index) ? 'a.' . $this->index : 1);
            $result['results'] = $this->db->find($this->table . ' a ' . implode(' ', $str), $cond, $order, $page, $limit, '', $select, $group);
            $result['next_page'] = ceil($result['total'] / $limit) > $result['page'];
        } else {
            if ($page == 0 && $limit == 1) {
                $result = $this->db->find_one($this->table . ' a ' . implode(' ', $str), $cond, $order, $select);
            } else {
                $result = $this->db->find($this->table . ' a ' . implode(' ', $str), $cond, $order, $page, $limit, '', $select, $group);
            }
        }
        return $result;
    }


   public function cache_read($cond, $select = '*')
    {
        $k = md5(xn_json_encode($cond));
        $data = $this->cache->get($k);
        if (!$data) {
            $data = $this->read($cond, '*');
            $this->cache->set($k, $data, 3600);
        }
        if (is_string($select) && $select != '*') {
            $select = explode(',', $select);
        }
        if ($select != '*') {
            $_data = $data;
            $data = [];
            foreach ($select as $v) {
                if ($v) {
                    $data[$v] = $_data[$v];
                }
            }
        }
        return $data;
    }

    public function cache_find_one($cond, $order = [], $select = '*')
    {
        return $this->find_one($cond, $order, $select);
    }

    public function insertGetId($data)
    {
        return $this->insert($data);
    }

}

?>