<?php
/**
 * Created by PhpStorm.
 * User: yinhanmin
 * Date: 2019-01-30
 * Time: 15:49
 */

// hook common_start.php

function ConfigReload()
{
    // hook common_ConfigReload_brfore.php
    $conf = include __CONDIR__ . __LV__ . '_config.php';
    $conf['url_suffix'] = explode('|', $conf['url_suffix']);

    $_ENV['conf'] = $conf;
    $_ENV['err_code'] = include __CONDIR__ .  'Error.php';
    $_ENV['mines'] = include _include(__CONDIR__ . 'Mines.php');

    $_ENV['dispatcher'] = FastRoute\simpleDispatcher(function (FastRoute\RouteCollector $r) {
        // hook common_route_before.php

        //$r->addRoute(['GET', 'POST'], 'forum-{fid:\d+}[-{page:\d+}]', 'index/forum/index');

        // hook common_route_after.php
    });

    // hook common_ConfigReload_after.php
}

function IncludeFiles()
{

    // hook common_IncludeFiles_brfore.php

    require_once _include(__APPDIR__ . 'Model.php');
    require_once _include(__APPDIR__ . 'Controller/Controller.php');

    // hook common_IncludeFiles_public_before.php

    $include_public_files = glob(__PUBDIR__ . '*.php');
    foreach ($include_public_files as $public_file) {
        require_once _include($public_file);
    }


    // hook common_IncludeFiles_model_before.php

    $is_load = [];
    foreach ($_ENV['plugin_model_files'] as $k => $model_file) {
        $is_load[$k] = 1;
        require_once _include($model_file);
    }

    $include_model_files = glob(__APPDIR__ . 'Model/*.php');
    foreach ($include_model_files as $model_file) {
        $name = str_replace([__APPDIR__ . "Model/", 'Model.php'], '', $model_file);
        if (!isset($is_load[$name])) {
            require_once _include($model_file);
        }
    }

    // hook common_IncludeFiles_controller_before.php

    $controller_files = glob(__APPDIR__ . 'Controller/*.php');
    foreach ($controller_files as $controller_file) {
        if ($controller_file == __APPDIR__ . 'Controller/Controller.php') {
            continue;
        }

        require_once _include($controller_file);
    }

    $is_load = [];
    foreach ($_ENV['plugin_controllers_files'] as $k => $controllers_file) {
        $is_load[$k] = 1;
        require_once _include($controllers_file);
    }

    $controllers_files = glob(__APPDIR__ . "Controller/*/*.php"); // path
    if (is_array($controllers_files)) {
        foreach ($controllers_files as $k => $controllers_file) {
            $name = str_replace([__APPDIR__ . "Controller/", '.php'], '', $controllers_file);
            $name = '\\' . str_replace('/', '\\', $name);
            if (!isset($is_load[$name])) {
                require_once _include($controllers_file);
            }
        }
    }
    // hook common_IncludeFiles_after.php
}


// hook common_end.php
?>