<?php

/*
	中国国情下的判断浏览器类型，简直就是五代十国，乱七八糟，对博主的收集表示感谢

	参考：
	http://www.cnblogs.com/wangchao928/p/4166805.html
	http://www.useragentstring.com/pages/Internet%20Explorer/
	https://github.com/serbanghita/Mobile-Detect/blob/master/Mobile_Detect.php

	Mozilla/4.0 (compatible; MSIE 5.0; Windows NT)
	Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1)
	Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.2)
	Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)

	Win7+ie9：
	Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; Win64; x64; Trident/5.0; .NET CLR 2.0.50727; SLCC2; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; .NET4.0C; Tablet PC 2.0; .NET4.0E)

	win7+ie11，模拟 78910 头是一样的
	mozilla/5.0 (windows nt 6.1; wow64; trident/7.0; rv:11.0) like gecko

	Win7+ie8：
	Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.1; WOW64; Trident/4.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; .NET4.0C; InfoPath.3)

	WinXP+ie8：
	Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 5.1; Trident/4.0; GTB7.0)

	WinXP+ie7：
	Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 5.1)

	WinXP+ie6：
	Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1)

	傲游3.1.7在Win7+ie9,高速模式:
	Mozilla/5.0 (Windows; U; Windows NT 6.1; ) AppleWebKit/534.12 (KHTML, like Gecko) Maxthon/3.0 Safari/534.12

	傲游3.1.7在Win7+ie9,IE内核兼容模式:
	Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; .NET4.0C; .NET4.0E)

	搜狗
	搜狗3.0在Win7+ie9,IE内核兼容模式:
	Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; .NET4.0C; .NET4.0E; SE 2.X MetaSr 1.0)

	搜狗3.0在Win7+ie9,高速模式:
	Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US) AppleWebKit/534.3 (KHTML, like Gecko) Chrome/6.0.472.33 Safari/534.3 SE 2.X MetaSr 1.0

	360
	360浏览器3.0在Win7+ie9:
	Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; .NET4.0C; .NET4.0E)

	QQ 浏览器
	QQ 浏览器6.9(11079)在Win7+ie9,极速模式:
	Mozilla/5.0 (Windows NT 6.1) AppleWebKit/535.1 (KHTML, like Gecko) Chrome/13.0.782.41 Safari/535.1 QQBrowser/6.9.11079.201

	QQ浏览器6.9(11079)在Win7+ie9,IE内核兼容模式:
	Mozilla/4.0 (compatible; MSIE 7.0; Windows NT 6.1; WOW64; Trident/5.0; SLCC2; .NET CLR 2.0.50727; .NET CLR 3.5.30729; .NET CLR 3.0.30729; Media Center PC 6.0; InfoPath.3; .NET4.0C; .NET4.0E) QQBrowser/6.9.11079.201

	阿云浏览器
	阿云浏览器 1.3.0.1724 Beta 在Win7+ie9:
	Mozilla/5.0 (compatible; MSIE 9.0; Windows NT 6.1; WOW64; Trident/5.0)

	MIUI V5
	Mozilla/5.0 (Linux; U; Android <android-version>; <location>; <MODEL> Build/<ProductLine>) AppleWebKit/534.30 (KHTML, like Gecko) Version/4.0 Mobile Safari/534.30 XiaoMi/MiuiBrowser/1.0
*/


/**
 * 获取控制器节点列表
 * @return array
 * @throws \ReflectionException
 */
function getClassList()
{

    eachController(function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
        list($node, $comment) = [trim($prenode, ' / '), $reflection->getDocComment()];
        $nodes[$node] = preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', preg_replace("/\s/", '', $comment));
        if (stripos($nodes[$node], '@') !== false) $nodes[$node] = '';
    });
    return $nodes;
}

/**
 * 获取方法节点列表
 * @return array
 * @throws \ReflectionException
 */
function getMethodList()
{
    eachController(function (\ReflectionClass $reflection, $prenode) use (&$nodes) {
        $class_name = $reflection->getName();
        foreach ($reflection->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
            if ($class_name != $method->class) {
                continue;
            }
            $action = strtolower($method->getName());
            list($node, $comment) = ["{$prenode}{$action}", preg_replace("/\s/", '', $method->getDocComment())];
            $nodes[$node] = [
                'auth' => stripos($comment, '@authtrue') !== false,
                'menu' => stripos($comment, '@menutrue') !== false,
                'login' => stripos($comment, '@logintrue') !== false,
                'title' => preg_replace('/^\/\*\*\*(.*?)\*.*?$/', '$1', $comment),
            ];
            if (stripos($nodes[$node]['title'], '@') !== false) $nodes[$node]['title'] = '';
        }
    });
    return $nodes;
}

/**
 * 控制器扫描回调
 * @param callable $callable
 * @throws \ReflectionException
 */
function eachController($callable)
{
    $_files = [];
    foreach ($_ENV['plugin_controllers_files'] as $file) {
        $matches = [];
        if (!preg_match("|Controller/(\w+)/(.+)\.php$|", $file, $matches)) continue;
        list($module, $controller) = [$matches[1], strtr($matches[2], '/', '.')];
        $class = "\\{$module}\\{$controller}";
        $_files[$class]=1;
        if (is_file($file)) {
            //require_once _include($file);
            if (class_exists($class)) {
                call_user_func($callable, new \ReflectionClass($class), parseString("{$module}/{$controller}/"));
            }
        }
    }

    foreach (scanPath(__APPDIR__ . "Controller/*") as $file) {
        $matches = [];
        if (!preg_match("|Controller/(\w+)/(.+)\.php$|", $file, $matches)) continue;
        list($module, $controller) = [$matches[1], strtr($matches[2], '/', '.')];
        $class = "\\{$module}\\{$controller}";
        if (!$_files[$class] && is_file(__APPDIR__ . $matches[0])) {
            //require_once _include(__APPDIR__ . $matches[0]);
            if (class_exists($class)) {
                call_user_func($callable, new \ReflectionClass($class), parseString("{$module}/{$controller}/"));
            }

        }
    }
}

/**
 * 驼峰转下划线规则
 * @param string $node 节点名称
 * @return string
 */
function parseString($node)
{
    if (count($nodes = explode('/', $node)) > 1) {
        $dots = [];
        foreach (explode('.', $nodes[1]) as $dot) {
            $dots[] = trim(preg_replace("/[A-Z]/", "_\\0", $dot), "_");
        }
        $nodes[1] = join('.', $dots);
    }
    return strtolower(join('/', $nodes));
}

/**
 * 获取所有PHP文件
 * @param string $dirname 扫描目录
 * @param array $data 额外数据
 * @param string $ext 有文件后缀
 * @return array
 */
function scanPath($dirname, $data = [], $ext = 'php')
{
    foreach (glob("{$dirname}*") as $file) {
        if (is_dir($file)) {
            $data = array_merge($data, scanPath("{$file}/"));
        } elseif (is_file($file) && pathinfo($file, 4) === $ext) {
            $data[] = str_replace('\\', '/', $file);
        }
    }
    return $data;
}


function image_ext($filename)
{
    return strtolower(substr(strrchr($filename, '.'), 1));
}

// 获取安全的文件名，如果文件存在，则加时间戳和随机数，避免重复
function image_safe_name($filename, $dir)
{
    $attach = include _include(__CONDIR__.'attach.conf.php');
    $time = $GLOBALS['time'];
    // 最后一个 . 保留，其他的 . 替换
    $s1 = substr($filename, 0, strrpos($filename, '.'));
    $s2 = substr(strrchr($filename, '.'), 1);
    !in_array($s2, $attach['all']) AND $s2 = $s2;
    if (is_file($dir . "$s1.$s2")) {
        $newname = $s1 . $time . rand(1, 1000) . '.' . $s2;
    } else {
        $newname = "$s1.$s2";
    }
    return $newname;
}


function file_copy($file, $uid)
{
    mt_srand();
    $dir = date('Ym/');
    $ext = '.' . ltrim($file['type'], 'image/');
    $name = $uid . '_' . time() . mt_rand(1000, 9999) . $ext;
    $destfile = image_safe_name($name, __UPFDIR__ .'/'. $dir);
    $url = __UPPATH__ . $dir . $destfile;
    !is_dir(__UPFDIR__ .'/'. $dir) AND mkdir(__UPFDIR__ .'/'. $dir, 0777, 1) && chmod(__UPFDIR__ .'/'. $dir, 0777);
    copy($file['tmp_name'], __UPFDIR__ . '/'.$dir . $destfile);
    is_file($file['tmp_name']) && unlink($file['tmp_name']);
    return ['src' => $url, 'filename' => $dir . $destfile];
}

function str_sql_k($k)
{
    $arr = explode('.', $k);
    if (!empty($arr[1])) {
        return $arr[0] . '.`' . $arr[1] . '`';
    } else {
        return '`' . $k . '`';
    }
}


function db_cond_to_sqladd($cond)
{
    $s = '';

    if (!empty($cond)) {
        $s = ' WHERE ';
        foreach ($cond as $k => $v) {
            $_k = str_sql_k($k);

            if (!is_array($v)) {
                $v = (is_int($v) || is_float($v)) ? $v : "'" . addslashes($v) . "'";
                $s .= "$_k=$v AND ";
            } elseif (isset($v[0])) {
                $ids = implode('\',\'', $v);
                $s .= "$_k IN ('$ids') AND ";
            } elseif (is_array($v) && empty($v)) {
                $s .= "$_k = '-' AND ";
            } else {
                foreach ($v as $k1 => $v1) {
                    if ($k1 == 'OR') {
                        $s = substr($s, 0, -4);
                        $s .= " OR $_k=$v1 AND ";
                    } elseif ($k1 == 'FIND_IN_SET') {
                        $s .= " FIND_IN_SET($v1,$_k) AND ";
                    } else {
                        if ($k1 == 'LLIKE') {
                            $k1 = ' LIKE ';
                            $v1 = "%$v1";
                        } elseif ($k1 == 'RLIKE') {
                            $k1 = ' LIKE ';
                            $v1 = "$v1%";
                        } elseif ($k1 == 'LIKE') {
                            $k1 = ' LIKE ';
                            $v1 = "%$v1%";
                        }
                        $v1 = (is_int($v1) || is_float($v1)) ? $v1 : "'" . addslashes($v1) . "'";
                        $s .= "$_k$k1$v1 AND ";
                    }

                }
            }
        }
        $s = substr($s, 0, -4);
    }
    return $s;
}

function db_orderby_to_sqladd($orderby)
{
    $s = '';
    if (!empty($orderby)) {
        $s .= ' ORDER BY ';
        $comma = '';
        foreach ($orderby as $k => $v) {
            $_k = str_sql_k($k);
            $s .= $comma . "$_k " . ($v == 1 ? ' ASC ' : ' DESC ');
            $comma = ',';
        }
    }

    return $s;
}


function db_array_to_update_sqladd($arr)
{
    $s = '';
    foreach ($arr as $k => $v) {
        $v = addslashes($v);
        $op = substr($k, -1);
        if ($op == '=') {
            $k = substr($k, 0, -1);
            $_k = str_sql_k($k);
            $s .= "$_k=$v,";
        } elseif ($op == '+' || $op == '-') {
            $k = substr($k, 0, -1);
            $_k = str_sql_k($k);
            $v = (is_int($v) || is_float($v)) ? $v : "'$v'";
            $s .= "$_k=$_k$op$v,";
        } else {
            $_k = str_sql_k($k);
            $v = (is_int($v) || is_float($v)) ? $v : "'$v'";
            $s .= "$_k=$v,";
        }
    }
    return substr($s, 0, -1);
}

/*
    $arr = array(
        'name'=>'abc',
        'date'=>12345678900,
    )
    db_array_to_insert_sqladd($arr);
*/
function db_array_to_insert_sqladd($arr)
{
    $s = '';
    $keys = array();
    $values = array();
    foreach ($arr as $k => $v) {
        $k = addslashes($k);
        $v = addslashes($v);
        $keys[] = '`' . $k . '`';
        $v = (is_int($v) || is_float($v)) ? $v : "'$v'";
        $values[] = $v;
    }
    $keystr = implode(',', $keys);
    $valstr = implode(',', $values);
    $sqladd = "($keystr) VALUES ($valstr)";
    return $sqladd;
}
//
//
//function get__browser()
//{
//
//    $agent = _S('user-agent');
//    $browser = '';
//    $browser_ver = '';
//
//    if (preg_match('/OmniWeb\/(v*)([^\s|;]+)/i', $agent, $regs)) {
//        $browser = 'OmniWeb';
//        $browser_ver = $regs[2];
//    }
//
//    if (preg_match('/Netscape([\d]*)\/([^\s]+)/i', $agent, $regs)) {
//        $browser = 'Netscape';
//        $browser_ver = $regs[2];
//    }
//
//    if (preg_match('/version\/(\d+).+safari\//i', $agent, $regs)) {
//        $browser = 'Safari';
//        $browser_ver = $regs[1];
//    }
//
//    if (preg_match('/MSIE\s([^\s|;]+)/i', $agent, $regs)) {
//        $browser = 'Internet Explorer';
//        $browser_ver = $regs[1];
//    }
//
//    if (preg_match('/Opera[\s|\/]([^\s]+)/i', $agent, $regs)) {
//        $browser = 'Opera';
//        $browser_ver = $regs[1];
//    }
//
//    if (preg_match('/NetCaptor\s([^\s|;]+)/i', $agent, $regs)) {
//        $browser = '(Internet Explorer ' . $browser_ver . ') NetCaptor';
//        $browser_ver = $regs[1];
//    }
//
//    if (preg_match('/Maxthon\/([^\s]+)/i', $agent, $regs)) {
//        $browser = '(Internet Explorer ' . $browser_ver . ') Maxthon';
//        $browser_ver = $regs[1];
//    }
//    if (preg_match('/360SE/i', $agent, $regs)) {
//        $browser = '(Internet Explorer ' . $browser_ver . ') 360SE';
//        $browser_ver = '';
//    }
//    if (preg_match('/SE 2.x/i', $agent, $regs)) {
//        $browser = '(Internet Explorer ' . $browser_ver . ') 搜狗';
//        $browser_ver = '';
//    }
//
//    if (preg_match('/FireFox\/([^\s]+)/i', $agent, $regs)) {
//        $browser = 'FireFox';
//        $browser_ver = intval($regs[1]);
//    }
//
//    if (preg_match('/Lynx\/([^\s]+)/i', $agent, $regs)) {
//        $browser = 'Lynx';
//        $browser_ver = $regs[1];
//    }
//
//    if (preg_match('/Chrome\/([^\s]+)/i', $agent, $regs)) {
//        $browser = 'Chrome';
//        $browser_ver = intval($regs[1]);
//    }
//    if (preg_match('/MicroMessenger\/([^\s]+)/i', $agent, $regs)) {
//        $browser = 'MicroMessenger';
//        $browser_ver = $regs[1];
//    }
//
//    if ($browser != '') {
//        return [
//            'browser' => $browser,
//            'version' => $browser_ver
//        ];
//    } else {
//        return [
//            'browser' => 'unknow',
//            'version' => 0
//        ];
//    }
//}


function getDistance($lat1, $lng1, $lat2, $lng2)
{
    //地球半径
    $R = 6378137;
    //将角度转为狐度
    $radLat1 = deg2rad($lat1);
    $radLat2 = deg2rad($lat2);
    $radLng1 = deg2rad($lng1);
    $radLng2 = deg2rad($lng2);
    //结果
    $s = acos(cos($radLat1) * cos($radLat2) * cos($radLng1 - $radLng2) + sin($radLat1) * sin($radLat2)) * $R;
    //精度
    $s = round($s * 10000) / 10000;

    return round($s);
}

/**
 * 此处为IDE自动提示准备，故而始终加载旧文件
 * @param        $srcfile
 * @param string $old
 * @return string
 */
function IDE_include($srcfile, $old = '')
{
    if ($old) {
        static $len;
        $len = $len ? $len : strlen(dirname(__ROTDIR__));
        $tmpfile = __IDEDIR__ . substr(str_replace('/', '_', $srcfile), $len);
        $s = file_get_contents_try($srcfile);
        if (empty($s)) return $srcfile;
        // $s = str_replace('class Model','class Model extends \Server\Db\Query ',$s);
        $s = preg_replace('#//IDE_LOAD_START.+//IDE_LOAD_END#is', "\n" . $old . "\n", $s);
        !empty($s) AND file_put_contents_try($tmpfile, $s);
    }
    return $srcfile;
}

function load_file($m)
{
    $file_name = __TMPDIR__ . $m['1'] . '.php';
    $s = '';
    if (is_file($file_name)) {
        $s = ltrim(file_read($file_name), '<?php exit();');
    }
    return $s;
}

function file_name($path)
{
    return substr($path, strrpos($path, '/') + 1);
}

// 将变量写入到文件，根据后缀判断文件格式，先备份，再写入，写入失败，还原备份
function file_replace_var($filepath, $replace = array(), $pretty = FALSE)
{
    $ext = file_ext($filepath);
    if ($ext == 'php') {
        $arr = include $filepath;
        $arr = array_merge($arr, $replace);
        $s = "<?php\r\nreturn " . var_export($arr, true) . ";\r\n?>";
        // 备份文件
        file_backup($filepath);
        $r = file_put_contents_try($filepath, $s);
        $r != strlen($s) ? file_backup_restore($filepath) : file_backup_unlink($filepath);
        return $r;
    } elseif ($ext == 'js' || $ext == 'json') {
        $s = file_get_contents_try($filepath);
        $arr = xn_json_decode($s);
        if (empty($arr)) return FALSE;
        $arr = array_merge($arr, $replace);
        $s = xn_json_encode($arr);
        file_backup($filepath);
        $r = file_put_contents_try($filepath, $s);
        $r != strlen($s) ? file_backup_restore($filepath) : file_backup_unlink($filepath);
        return $r;
    }
}

function file_backname($filepath)
{
    $filepre = file_pre($filepath);
    $fileext = file_ext($filepath);
    $s = "$filepre.backup.$fileext";
    return $s;
}

function file_pre($filename, $max = 32)
{
    return substr($filename, 0, strrpos($filename, '.'));
}

function is_backfile($filepath)
{
    return strpos($filepath, '.backup.') !== FALSE;
}

// 备份文件
function file_backup($filepath)
{
    $backfile = file_backname($filepath);
    if (is_file($backfile)) return TRUE; // 备份已经存在
    $r = xn_copy($filepath, $backfile);
    clearstatcache();
    return $r && filesize($backfile) == filesize($filepath);
}

// 还原备份
function file_backup_restore($filepath)
{
    $backfile = file_backname($filepath);
    $r = xn_copy($backfile, $filepath);
    clearstatcache();
    $r && filesize($backfile) == filesize($filepath) && xn_unlink($backfile);
    return $r;
}

// 删除备份
function file_backup_unlink($filepath)
{
    $backfile = file_backname($filepath);
    $r = xn_unlink($backfile);
    return $r;
}

function file_ext($filename, $max = 16)
{
    $ext = strtolower(substr(strrchr($filename, '.'), 1));
    $ext = xn_urlencode($ext);
    strlen($ext) > $max AND $ext = substr($ext, 0, $max);
    if (!preg_match('#^\w+$#', $ext)) $ext = 'attach';
    return $ext;
}

function formatSize($b, $times = 0)
{
    if ($b > 1024) {
        $temp = $b / 1024;
        return formatSize($temp, $times + 1);
    } else {
        $unit = 'B';
        switch ($times) {
            case '0':
                $unit = 'B';
                break;
            case '1':
                $unit = 'KB';
                break;
            case '2':
                $unit = 'MB';
                break;
            case '3':
                $unit = 'GB';
                break;
            case '4':
                $unit = 'TB';
                break;
            case '5':
                $unit = 'PB';
                break;
            case '6':
                $unit = 'EB';
                break;
            case '7':
                $unit = 'ZB';
                break;
            default:
                $unit = '单位未知';
        }
        return sprintf('%.2f', $b) . $unit;
    }
}

function file_get_contents_try($file, $times = 3)
{
    while ($times-- > 0) {
        $fp = fopen($file, 'rb');
        if ($fp) {
            $size = filesize($file);
            if ($size == 0) return '';
            $s = fread($fp, $size);
            fclose($fp);
            return $s;
        } else {
            sleep(1);
        }
    }
    return FALSE;
}

function file_put_contents_try($file, $s, $times = 3)
{
    while ($times-- > 0) {
        $fp = fopen($file, 'wb');
        if ($fp AND flock($fp, LOCK_EX)) {
            $n = fwrite($fp, $s);
            flock($fp, LOCK_UN);
            fclose($fp);
            clearstatcache();
            return $n;
        } else {
            usleep(100);
        }
    }
    return false;
}

function get_file_methods($file)
{
    $s = php_strip_whitespace($file);
    if (empty($s)) return [];
    preg_match_all("#function(.+)\(#isU", $s, $arr);
    $list = [];
    foreach ($arr[1] as $v) {
        $list[] = trim($v);
    }
    return $list;
}

function convertUnderline($str)
{
    $str = ucwords(str_replace('-', ' ', strtolower($str)));
    return str_replace(' ', '', $str);
    //return $ucfirst ? ucfirst($str) : $str;
}

function humpToLine($str)
{
    $str = preg_replace_callback('/([A-Z]{1})/', function ($matches) {
        return '-' . strtolower($matches[0]);
    }, $str);
    return ltrim($str, '-');
}

function xn_log($s, $file = 'error')
{
    $day = date('Ym');
    $logpath = __LOGDIR__ . $day;
    !is_dir($logpath) AND mkdir($logpath, 0777, 1) && chmod($logpath, 0777);
    $fp = fopen($logpath . '/' . date('d') . '_' . $file . '.log', "a+");
    fwrite($fp, date('His') . "\t" . $s . "\r\n");
    fclose($fp);
    //error_log(date('His') . "\t" . $s . "\r\n", 3, $logpath . '/' . date('d') . '_' . $file . '.log');
}


function stat_log($s, $file = 'stat')
{
    $day = date('Ymd');
    $logpath = __STADIR__ . $day . '_' . $file . '.log';
    $fp = fopen($logpath, "a+");
    fwrite($fp, date('H:i') . "\t" . implode("\t", $s) . "\r\n");
    fclose($fp);
    //error_log(date('H:i') . "\t" . implode("\t", $s) . "\r\n", 3, $logpath);
}

function read_stat_log($day = '', $file = 'stat')
{
    $logpath = __STADIR__ . $day . '_' . $file . '.log';
    return is_file($logpath) ? file_read($logpath) : false;
}

function read_stat_log_day($file = 'stat')
{
    $include_model_files = glob(__STADIR__ . '*' . $file . '.log');
    foreach ($include_model_files as $model_files) {
        $str[] = str_replace([__STADIR__, '_' . $file . '.log'], '', $model_files);
    }
    return $str;
}

function getut()
{
    list($usec, $sec) = explode(" ", microtime());
    return bcadd($usec, $sec, 6);
}

function getum()
{
    return function_exists('memory_get_usage') ? memory_get_usage() : 0;
}


function xn_url_parse($request_url)
{
    $request_url = ltrim(str_replace('/?', '/', $request_url), '/');
    $request = explode('?', $request_url);
    $r = explode('/', $request[0]);
    if (!empty($request[1])) {
        parse_str($request[1], $arr);
        $r += $arr;
    }
    return $r;
}

function parseName($name, $type = 0, $ucfirst = true)
{
    if ($type) {
        $name = preg_replace_callback('/_([a-zA-Z])/', function ($match) {
            return strtoupper($match[1]);
        }, $name);
        return $ucfirst ? ucfirst($name) : lcfirst($name);
    } else {
        return strtolower(trim(preg_replace("/[A-Z]/", "_\\0", $name), "_"));
    }
}

function file_read($file)
{
    $fp = fopen($file, "r");
    $readData = '';
    $chunk = 4096;
    $size = filesize($file);
    $fs = sprintf("%u", $size);
    $max = (intval($fs) == PHP_INT_MAX) ? PHP_INT_MAX : $size;
    for ($len = 0; $len < $max; $len += $chunk) {
        $seekSize = ($max - $len > $chunk) ? $chunk : $max - $len;
        fseek($fp, ($len + $seekSize) * -1, SEEK_END);
        $readData = fread($fp, $seekSize) . $readData;
    }
    fclose($fp);
    return $readData;
}

function ip($type = 0)
{
    $type = $type ? 1 : 0;
    if (isset($_SERVER['X_FORWARDED_FOR'])) {
        $ip = _S('X_FORWARDED_FOR');
        $arr = array_filter(explode(',', $ip));
        $ip = trim(end($arr));
    } else {
        $ip = _S('REMOTE_ADDR');
    }
    $long = sprintf("%u", ip2long($ip));
    $ip = $long ? array($ip, $long) : array('0.0.0.0', 0);
    return $ip[$type];
}


function xn_safe_key($key = '')
{
    return $key ? $key : _CONF('auth_key');
}

function xn_urlencode($s)
{
    $s = str_replace('-', '_2d', $s);
    $s = str_replace('.', '_2e', $s);
    $s = str_replace('+', '_2b', $s);
    $s = str_replace('=', '_3d', $s);
    $s = urlencode($s);
    $s = str_replace('%', '_', $s);

    return $s;
}

function xn_urldecode($s)
{
    $s = str_replace('_', '%', $s);
    $s = urldecode($s);

    return $s;
}

function createLetterRange($length)
{
    $range = array();
    $letters = range('A', 'Z');
    for ($i = 0; $i < $length; $i++) {
        $position = $i * 26;
        foreach ($letters as $ii => $letter) {
            $position++;
            if ($position <= $length) $range[] = ($position > 26 ? $range[$i - 1] : '') . $letter;
        }
    }
    return $range;
}

function getGuid()
{
    if (function_exists('com_create_guid') === true) {
        return trim(com_create_guid(), '{}');
    }
    return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X', mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(16384, 20479), mt_rand(32768, 49151), mt_rand(0, 65535), mt_rand(0, 65535), mt_rand(0, 65535));
}

/**
 * @return string
 * 消耗时间
 */
function ut($starttime)
{
    $ut = bcmul(bcsub(getut(), $starttime, 7), 1000, 5) . 'Ms';
    return $ut;
}

function ms_log($starttime)
{
    return bcmul(bcsub(getut(), $starttime, 7), 10000, 0);
}

/**
 * @return string
 * 消耗内存
 */
function um($startmemory)
{
    return bcdiv(bcsub(getum(), $startmemory), 1024, 4) . 'Kb';
}


function Xcopy($source, $destination, $child = 1)
{

    //用法：
    // xCopy("feiy","feiy2",1):拷贝feiy下的文件到 feiy2,包括子目录
    // xCopy("feiy","feiy2",0):拷贝feiy下的文件到 feiy2,不包括子目录
    //参数说明：
    // $source:源目录名
    // $destination:目的目录名
    // $child:复制时，是不是包含的子目录

    if (!is_dir($source)) {
        return false;
    }

    if (!is_dir($destination)) {
        mkdir($destination, 0777) && chmod($destination, 0777);
    }

    $handle = dir($source);
    while ($entry = $handle->read()) {
        if (($entry != ".") && ($entry != "..")) {
            if (is_dir($source . "/" . $entry)) {
                $child AND Xcopy($source . "/" . $entry, $destination . "/" . $entry, $child);
            } else {
                copy($source . "/" . $entry, $destination . "/" . $entry);
            }
        }
    }
    closedir($source);
    return true;
}

function Xremove($source, $destination, $child = 1)
{

    //用法：
    // xCopy("feiy","feiy2",1):拷贝feiy下的文件到 feiy2,包括子目录
    // xCopy("feiy","feiy2",0):拷贝feiy下的文件到 feiy2,不包括子目录
    //参数说明：
    // $source:源目录名
    // $destination:目的目录名
    // $child:复制时，是不是包含的子目录

    if (!is_dir($source)) {
        return false;
    }

    $handle = dir($source);
    while ($entry = $handle->read()) {
        if (($entry != ".") && ($entry != "..")) {
            if (is_dir($source . "/" . $entry)) {
                $child AND Xremove($source . "/" . $entry, $destination . "/" . $entry, $child);
            } else {
                unlink($destination . "/" . $entry);
            }
        }
    }
    closedir($source);
    xn_rmdir($destination);
    return true;
}


// 递归遍历目录
function glob_recursive($pattern, $flags = 0)
{
    $files = glob($pattern, $flags);
    foreach (glob(dirname($pattern) . '/*', GLOB_ONLYDIR | GLOB_NOSORT) as $dir) {
        $files = array_merge($files, glob_recursive($dir . '/' . basename($pattern), $flags));
    }
    return $files;
}

function rmdir_tmp()
{
    $files = glob(__TMPDIR__ . '*.php');
    foreach ($files as $file) {
        xn_unlink($file);
    }
}

//
//// 递归删除目录，这个函数比较危险，传参一定要小心
function rmdir_recusive($dir, $keepdir = 0)
{
    if ($dir == '/' || $dir == './' || $dir == '../') return FALSE;// 不允许删除根目录，避免程序意外删除数据。
    if (!is_dir($dir)) return FALSE;
    substr($dir, -1) != '/' AND $dir .= '/';
    $files = glob($dir . '*'); // +glob($dir.'.*')
    foreach (glob($dir . '.*') as $v) {
        if (substr($v, -1) != '.' && substr($v, -2) != '..') $files[] = $v;
    }
    $filearr = $dirarr = array();
    if ($files) {
        foreach ($files as $file) {
            if (is_dir($file)) {
                $dirarr[] = $file;
            } else {
                $filearr[] = $file;
            }
        }
    }
    if ($filearr) {
        foreach ($filearr as $file) {
            xn_unlink($file);
        }
    }
    if ($dirarr) {
        foreach ($dirarr as $file) {
            rmdir_recusive($file);
        }
    }
    if (!$keepdir) xn_rmdir($dir);
    return TRUE;
}


function xn_copy($src, $dest)
{
    $r = is_file($src) ? copy($src, $dest) : FALSE;
    return $r;
}

function xn_rmdir($dir)
{
    $r = is_dir($dir) ? rmdir($dir) : FALSE;
    return $r;
}

function xn_unlink($file)
{
    $r = is_file($file) ? unlink($file) : FALSE;
    return $r;
}

function xn_filemtime($file)
{
    return is_file($file) ? filemtime($file) : 0;
}

/**
 * User: zhixiang
 *  Explain:
 *  - 生成指定长度 唯一编号
 *
 * @param     $num
 * @param int $s
 *
 * @return bool|string
 */
function get_uniqid($num, $s = 0)
{

    return substr(md5(uniqid(microtime(true),true)), $s, $num);
}

function xn_json_encode($data, $pretty = FALSE, $level = 0)
{
    return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
}

function xn_json_decode($json)
{
    $json = trim($json, "\xEF\xBB\xBF");
    $json = trim($json, "\xFE\xFF");
    return json_decode($json, 1);
}

function _CONF($key='', $def = '')
{
    if(empty($key)){
        return $_ENV['conf'];
    }else{
        return isset($_ENV['conf'][$key]) ? $_ENV['conf'][$key] : $def;
    }
}


/*
function curl_get($url, $timeout = 5, $ref = '')
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    if (!empty($ref)) {
        curl_setopt($ch, CURLOPT_REFERER, $ref);
    }
    curl_setopt($ch, CURLOPT_FAILONERROR, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $reponse = curl_exec($ch);
    curl_close($ch);

    return $reponse;
}

function curl_post($url, $data, $https = 0)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
    if ($https == 1) {
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSLVERSION, 1);
    }
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $temp = curl_exec($ch);
    curl_close($ch);
    return $temp;
}


function https_get($url, $cookie = '', $timeout = 30)
{
    return https_post($url, '', $cookie, $timeout);
}

function https_post($url, $post = '', $cookie = '', $timeout = 30)
{

    $ch = curl_init();
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HEADER, 2); // 1/2
    curl_setopt($ch, CURLOPT_URL, $url);
    if (!empty($_SERVER['user-agent'])) {
        curl_setopt($ch, CURLOPT_USERAGENT, $_SERVER['user-agent']);
    }
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0); // 对认证证书来源的检查
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2); // 从证书中检查SSL加密算法是否存在，默认可以省略
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    if ($post) {
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
    }
    if ($cookie) {
        curl_setopt($ch, CURLOPT_HTTPHEADER, array("Cookie: $cookie"));
    }
    (!ini_get('safe_mode') && !ini_get('open_basedir')) && curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1); // 使用自动跳转, 安全模式不允许
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $data = curl_exec($ch);

    if (!$data) {
        curl_close($ch);

        return '';
    }

    list($header, $data) = explode("\r\n\r\n", $data);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if ($http_code == 301 || $http_code == 302) {
        $matches = array();
        preg_match('/Location:(.*?)\n/', $header, $matches);
        $url = trim(array_pop($matches));
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_HEADER, false);
        $data = curl_exec($ch);
    }
    curl_close($ch);

    return $data;
}

*/

function in_string($s, $str) {
    if(!$s || !$str) return FALSE;
    $s = ",$s,";
    $str = ",$str,";
    return strpos($str, $s) !== FALSE;
}


function xn_encrypt($txt, $key = '')
{
    empty($key) AND $key = xn_safe_key();
    return xn_urlencode(base64_encode(xxtea_encrypt($txt, $key)));
}

function xn_decrypt($txt, $key = '')
{
    empty($key) AND $key = xn_safe_key();
    return xxtea_decrypt(base64_decode(xn_urldecode($txt)), $key);
}


// ---------------------> encrypt function
function xxtea_long2str($v, $w)
{
    $len = count($v);
    $n = ($len - 1) << 2;
    if ($w) {
        $m = $v[$len - 1];
        if (($m < $n - 3) || ($m > $n)) return false;
        $n = $m;
    }
    $s = array();
    for ($i = 0; $i < $len; $i++) {
        $s[$i] = pack("V", $v[$i]);
    }
    if ($w) {
        return substr(join('', $s), 0, $n);
    } else {
        return join('', $s);
    }
}

function xxtea_str2long($s, $w)
{
    $v = unpack("V*", $s . str_repeat("\0", (4 - strlen($s) % 4) & 3));
    $v = array_values($v);
    if ($w) {
        $v[count($v)] = strlen($s);
    }
    return $v;
}

function xxtea_int32($n)
{
    while ($n >= 2147483648) $n -= 4294967296;
    while ($n <= -2147483649) $n += 4294967296;

    return (int)$n;
}

function xxtea_encrypt($str, $key)
{
    if ($str == '') return '';
    $v = xxtea_str2long($str, true);
    $k = xxtea_str2long($key, false);
    if (count($k) < 4) {
        for ($i = count($k); $i < 4; $i++) {
            $k[$i] = 0;
        }
    }
    $n = count($v) - 1;

    $z = $v[$n];
    $y = $v[0];
    $delta = 0x9E3779B9;
    $q = floor(6 + 52 / ($n + 1));
    $sum = 0;
    while (0 < $q--) {
        $sum = xxtea_int32($sum + $delta);
        $e = $sum >> 2 & 3;
        for ($p = 0; $p < $n; $p++) {
            $y = $v[$p + 1];
            $mx = xxtea_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ xxtea_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $z = $v[$p] = xxtea_int32($v[$p] + $mx);
        }
        $y = $v[0];
        $mx = xxtea_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ xxtea_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $z = $v[$n] = xxtea_int32($v[$n] + $mx);
    }
    return xxtea_long2str($v, false);
}

function xxtea_decrypt($str, $key)
{
    if ($str == '') return '';
    $v = xxtea_str2long($str, false);
    $k = xxtea_str2long($key, false);
    if (count($k) < 4) {
        for ($i = count($k); $i < 4; $i++) {
            $k[$i] = 0;
        }
    }
    $n = count($v) - 1;
    $z = $v[$n];
    $y = $v[0];
    $delta = 0x9E3779B9;
    $q = floor(6 + 52 / ($n + 1));
    $sum = xxtea_int32($q * $delta);
    while ($sum != 0) {
        $e = $sum >> 2 & 3;
        for ($p = $n; $p > 0; $p--) {
            $z = $v[$p - 1];
            $mx = xxtea_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ xxtea_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
            $y = $v[$p] = xxtea_int32($v[$p] - $mx);
        }
        $z = $v[$n];
        $mx = xxtea_int32((($z >> 5 & 0x07ffffff) ^ $y << 2) + (($y >> 3 & 0x1fffffff) ^ $z << 4)) ^ xxtea_int32(($sum ^ $y) + ($k[$p & 3 ^ $e] ^ $z));
        $y = $v[0] = xxtea_int32($v[0] - $mx);
        $sum = xxtea_int32($sum - $delta);
    }

    return xxtea_long2str($v, true);
}

?>