<?php

namespace Admin;

use Ctrl\AdminController;
// hook admin_other_use.php

Class Other extends AdminController
{

    // hook admin_other_start.php

    /**
     * Stat_PUT
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Stat_PUT()
    {
        // hook admin_other_stat_put_start.php
        return '';
    }

    /**
     * Log_PUT
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Log_PUT()
    {
        // hook admin_other_log_put_start.php
        return '';
    }

    /**
     * Cache_PUT
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Cache_PUT()
    {
        // hook admin_other_cache_put_start.php
        return '';
    }

    /**
     * Log_get
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Log_get()
    {
        // hook admin_other_log_get_start.php
        $data = $this->OperateLog->GetList([]);
        // hook admin_other_log_get_end.php
        $this->response('0000', ['data' => $data]);
    }

    /**
     * Cache_POST
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Cache_POST()
    {

        $tmp_clear = $this->request->param('tmp_clear', 0);
        $redis_clear = $this->request->param('redis_clear', 0);
        // hook admin_other_cache_post_start.php
        if ($tmp_clear) {
            rmdir_recusive(__TMPDIR__);
            //rmdir_recusive(__LOGDIR__);
            mkdir(__TMPDIR__, 0777, 1);
			chmod(__TMPDIR__, 0777);
            //mkdir(__LOGDIR__, 0777, 1);
            //$this->Stat->delete([]);
        }
        if ($redis_clear) {
            $this->User->CacheFlushAll();
        }
        $worker_reload = $this->request->param('worker_reload', 0);
        if ($worker_reload) {
            $this->http_server->reload($worker_reload == 1 ? true : false);
        }
        // hook admin_other_cache_post_end.php
        $this->response('0000');

    }

    /**
     * Stat_Get
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Stat_Get()
    {
        $time = $this->request->param('time');
        $time = empty($time) ? date('Ymd') : date('Ymd', strtotime($time));
        $_list = read_stat_log($time, 'api');
        $_list = explode(PHP_EOL, $_list);
        $list = ['api_ms' => [], 'api_m_count' => [], 'api_count' => []];
        foreach ($_list as $v) {
            $v = trim($v);
            if (empty($v)) {
                continue;
            }
            $d = [];
            list($d['ymd'], $d['api'], $d['count'], $d['ms']) = explode("\t", $v);
            $t = explode(':', $d['ymd']);
            $list['api_ms'][$d['api']]['count'] += $d['count'];
            $list['api_ms'][$d['api']]['ms'] += $d['ms'];
            $list['api_ms'][$d['api']]['api'] = $d['api'];
            $list['api_count']['count'] += $d['count'];
            $list['api_count']['ms'] += $d['ms'];
            $list['api_m_count'][$t[0]]['count'] += $d['count'];
            $list['api_m_count'][$t[0]]['ms'] += $d['ms'];
            $list['api_m_count'][$t[0]]['h'] = $t[0] . ':00:00-' . $t[0] . ':59:59';

        }
        $list['api_ms'] = array_values($list['api_ms']);
        $list['api_m_count'] = array_values($list['api_m_count']);
        foreach ($list['api_ms'] as $k => $v) {
            $list['api_ms'][$k]['ms'] = bcdiv($v['ms'], 10000, 5);
            $list['api_ms'][$k]['msp'] = bcdiv($list['api_ms'][$k]['ms'], $v['count'], 5);
        }

        foreach ($list['api_m_count'] as $k => $v) {
            $list['api_m_count'][$k]['ms'] = bcdiv($v['ms'], 10000, 5);
            $list['api_m_count'][$k]['msp'] = bcdiv($list['api_m_count'][$k]['ms'], $v['count'], 5);
        }

        $_list = read_stat_log($time, 'workers');
        $_list = explode(PHP_EOL, $_list);
        foreach ($_list as $v) {
            $v = trim($v);
            if (empty($v)) {
                continue;
            }
            $d = [];
            list($d['ymd'], $id, $d['request_count'], $max, $d['memory'], $d['sys1'], $d['sys2'], $d['sys3']) = explode("\t", $v);
            $list['count'][$d['ymd']]['memory'] += $d['memory'];

            if (empty($list['worker'][$id])) {
                $list['worker'][$id]['type'] = 'line';
                $list['worker'][$id]['smooth'] = 'true';
                $list['worker'][$id]['name'] = sprintf('%04s#', $id);
            }
            $list['worker'][$id]['data'][] = $d['request_count'];
            $list['worker'][$id]['memory'][] = $d['memory'];
            !in_array($d['ymd'], $list['worker_data'], 1) AND $list['worker_data'][] = $d['ymd'];
        }
        $list['count'] = array_values($list['count']);
        $list['worker'] = array_values($list['worker']);


        $_list = read_stat_log($time, 'sys');
        $_list = explode(PHP_EOL, $_list);

        $list['sys'][1]['type'] = 'line';
        $list['sys'][1]['smooth'] = 'true';
        $list['sys'][1]['name'] = '01分';
        $list['sys'][2]['type'] = 'line';
        $list['sys'][2]['smooth'] = 'true';
        $list['sys'][2]['name'] = '05分';
        $list['sys'][3]['type'] = 'line';
        $list['sys'][3]['smooth'] = 'true';
        $list['sys'][3]['name'] = '15分';

        foreach ($_list as $v) {
            $v = trim($v);
            if (empty($v)) {
                continue;
            }
            $d = [];
            list($d['ymd'], $d['sys1'], $d['sys2'], $d['sys3']) = explode("\t", $v);

            $list['sys'][1]['data'][] = bcadd($d['sys1'], 0, 2);
            $list['sys'][2]['data'][] = bcadd($d['sys2'], 0, 2);
            $list['sys'][3]['data'][] = bcadd($d['sys3'], 0, 2);

            !in_array($d['ymd'], $list['sys_data'], 1) AND $list['sys_data'][] = $d['ymd'];
        }
        $list['count'] = array_values($list['count']);
        $list['worker'] = array_values($list['worker']);

        $list['sys'] = array_values($list['sys']);

        $this->response('0000', ['data' => $list]);
    }

    /**
     * Working
     * @auth true
     * @login true
     * @menu false
     * @throws \Exception
     */
    public function Working()
    {

        $server = read_stat_log_day('workers');
        $data['server'] = [
            'min' => date('Y-m-d', strtotime(min($server) . ' 00:00:00')), 'max' => date('Y-m-d', strtotime(max($server) . ' 23:59:59')), 'today' => date('Y-m-d')
        ];
        $this->response('0000', ['data' => $data]);
    }


    public function Table(){
        $reload_table = $this->request->param('reload_table',0);
        if($reload_table==1){

            for ($i = 0; $i < 5; $i++) {
                $x = $i>0 ?$i:'';
                if (!empty($_ENV['conf']['db' . $x])) {
                    $datebase = $_ENV['conf']['db' . $x]['database_name'];
                    $data = $this->User->show_tables($datebase);
                    foreach ($data as $row ){
                        $table = $row['Name'];
                        $column = $this->User->show_columns($table);
                        $fields = $this->TableField->select(['dblink'=>$datebase,'table_name'=>$table]);
                        $fields = arrlist_key_values($fields,'field','table_name');
                        foreach ($column as $col){
                           unset($fields[$col['Field']]);
                           $field=$this->TableField->read(['dblink'=>$datebase,'table_name'=>$table,'field'=>$col['Field']]);
                            if(empty($field['field'])){
                                $this->TableField->insert(['dblink'=>$datebase,'table_name'=>$table,'field'=>$col['Field'],'comment'=>$col['Comment']]);
                            }else{
                                $this->TableField->update(['dblink'=>$datebase,'table_name'=>$table,'field'=>$col['Field']],['comment'=>$col['Comment']]);
                            }
                        }

                        foreach ($fields as $k => $_field){
                            $k AND $this->TableField->delete(['dblink'=>$datebase,'table_name'=>$table,'field'=>$k]);
                        }
                    }
                }
            }
        }

        $this->response('0000', ['data' => $data]);
    }

    // hook admin_other_end.php

}

?>