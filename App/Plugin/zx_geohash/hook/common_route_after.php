<?php exit;
$r->addRoute(['POST'], 'cash/cash', 'cash/index/index');
$r->addRoute(['POST'], 'cash/confirm', 'cash/index/index_confirm');

$r->addRoute(['POST'], 'reg/reg', 'reg/index/index');
$r->addRoute(['POST'], 'reg/getmsg', 'reg/index/index_getmsg');
$r->addRoute(['POST'], 'reg/doregister', 'reg/index/index_doregister');

$r->addRoute(['GET'], 'demo/dowload', 'down/index/index');


