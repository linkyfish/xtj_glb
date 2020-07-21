<?php
/**
 * Created by PhpStorm.
 * User: yinhanmin
 * Date: 2019-01-30
 * Time: 15:50
 */
// hook tick_worker_start.php
$http_server = $this->http_server;
$workerId = $serv->worker_id;
$istask = $serv->taskworker ? 'M' : 'W';
$workerIdk = $workerId . $istask;
$last = $serv->workers_stat->get($workerIdk);
if ($last != false) {
	$serv->workers_stat->set($workerIdk, ['request' => $last['request'] + 1, 'last_request' => 0, 'memory' => memory_get_usage()]);
} else {
	$serv->workers_stat->set($workerIdk, ['request' => 0, 'last_request' => 0, 'memory' => memory_get_usage()]);
}

$serv->tick(10000, function ($id) use ($serv, $workerIdk) {
	$d = $serv->stats();
	$last = $serv->workers_stat->get($workerIdk);
	if ($last != false) {
		$serv->workers_stat->set($workerIdk, ['request' => $last['request'] + ($d['worker_request_count'] - $last['last_request']), 'last_request' => $d['worker_request_count'], 'memory' => memory_get_usage()]);
	} else {
		$serv->workers_stat->set($workerIdk, ['request' => 0, 'last_request' => $d['worker_request_count'], 'memory' => memory_get_usage()]);
	}
});

if (__ISKF__ > 0) {
	$serv->tick(__ISKF__, function ($id) use ($serv) {
		$serv->reload();
	});
}
// hook tick_worker_before.php

if ($workerId == 0) {
	// hook tick_worker_0_start.php

	$this->http_server->tick(1000, function ($id) use ($http_server) {
		$time = time();
		if ($time % 300 == 0) {
			$works = $requst = [];
			foreach ($http_server->workers_stat as $k => $row) {
				$http_server->workers_stat->del($k);
				$works[$k] = $row;
			}
			foreach ($http_server->request_stat as $k => $row) {
				$http_server->request_stat->del($k);
				$requst[$k] = $row;
			}

			foreach ($works as $k => $row) {
				stat_log([$k, $row['request'] < 0 ? $row['request'] * -1 : $row['request'], $row['last_request'], $row['memory']], 'workers');
			}

			foreach ($requst as $k => $row) {
				stat_log([$k, $row['count'], $row['ms']], 'api');
			}
		}
	});

	$this->http_server->tick(1000, function ($id) {
		$time = time();
		if ($time % 60 == 0) {
			$v = sys_getloadavg();
			stat_log($v, 'sys');
		}
	});

	$this->http_server->tick(1000, function ($id) {
		if (date('s')==59 && date('i')%3==0) {
			$this->http_server->task([
				'controller' => 'task', 'action' => 'moneytrans_minute', 'data' => ['start'=>time()],
				//'controller' => 'task', 'action' => 'moneytrans_minute', 'data' => ['start'=>strtotime('2019-12-25 22:21:33')],
			]);
		}
		$his = date('His');
		if ($his == 201) {//零点过两分
			$this->http_server->task([
				'controller' => 'task', 'action' => 'moneytrans_minute', 'data' => ['start'=>time()-1200],
			]);
		}
	});

	//实时押注/分成数据
    $this->http_server->tick(30000, function ($id) {$this->http_server->task([
            'controller' => 'task', 'action' => 'task_useragent_sum', 'data' => ['start'=>time()],
        ]);
        xn_log('实时押注/分成数据 '.date('Y-m-d H:i:s'),'task_useragent_sum');
    });

    //发送前一天的分成到账号
    $this->http_server->tick(1000, function ($id) {
        $his = date('His');
        if ($his == 901) {//零点过九分分钟发放

            $this->http_server->task([
                'controller' => 'task', 'action' => 'task_usr_agent_money_to_account', 'data' => ['start'=>time()-1200],
            ]);
            xn_log('发送提成 '.date('Y-m-d H:i:s'),'task_usr_agent_money_to_account');
        }
    });

    $this->http_server->tick(60000, function ($id) {
            $this->http_server->task([
                'controller' => 'task', 'action' => 'task_usr_agent_user_reg_count', 'data' => ['start'=>time()],
            ]);
        xn_log('更新注册人数 '.date('Y-m-d H:i:s'),'task_usr_agent_user_reg_count');
    });

    $this->http_server->tick(10000, function ($id) {
		$this->http_server->task([
			'controller' => 'task', 'action' => 'playeroline', 'data' => [],
		]);
	});



	//跑马灯
	$this->http_server->tick(300000, function ($id) {
		$this->http_server->task([
			'controller' => 'task', 'action' => 'pushAd', 'data' => [],
		]);
	});


	//更新单控数据
	$this->http_server->tick(10000, function ($id) {
		$this->http_server->task([
			'controller' => 'task', 'action' => 'updateUserControl', 'data' => [],
		]);

	});


	//重置错误登录次数
	$this->http_server->tick(120000, function ($id) {
		$this->http_server->task([
			'controller' => 'task', 'action' => 'resetPlayerLoginFailNumber', 'data' => [],
		]);

	});

	$this->http_server->tick(60000, function ($id) {

		$this->http_server->task([
			'controller' => 'task', 'action' => 'updateIpRegion', 'data' => [],
		]);
	});

	$this->http_server->tick(600000, function ($id) {

		$this->http_server->task([
			'controller' => 'task', 'action' => 'updateUserTodayWin', 'data' => [],
		]);
	});

	$this->http_server->tick(10000, function ($id) {

		$this->http_server->task([
			'controller' => 'task', 'action' => 'updateShopFailSocoreOrders', 'data' => [],
		]);
	});


	$this->http_server->tick(60000, function ($id) {

		$this->http_server->task([
			'controller' => 'task', 'action' => 'updateIpNameList', 'data' => [],
		]);
	});

	$this->http_server->tick(60000, function ($id) {

		$this->http_server->task([
			'controller' => 'task', 'action' => 'updateSameNameCashList', 'data' => [],
		]);
	});
    $this->http_server->tick(20000, function ($id) {
        $this->http_server->task([
            'controller' => 'task', 'action' => 'regFirstSong', 'data' => [],
        ]);
    });


    $this->http_server->tick(60000, function ($id) {

        $this->http_server->task([
            'controller' => 'task', 'action' => 'updateOnlineUserLastRecharge', 'data' => [],
        ]);

    });

//    $this->http_server->tick(1000,function ($id){
//        if(date("H:i:s")=="00:05:00") {
//            $this->http_server->task([
//                'controller' => 'task', 'action' => 'settlement', 'data' => []
//            ]);
//        }
//    });

	// hook tick_worker_0_end.php

}
?>