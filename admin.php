<?php
/*
 * 后台入口文件
 * @author: lingeors;
 * @create_time: 2015-9-30 15:17:41；
 */

header("content-type: text/html; charset=utf8");

define('APP_PATH', dirname(__FILE__)); //定义网站根目录;
define('APP_NAME', 'Repair'); //定义APP名称
define('DEBUG', true); //调试模式

date_default_timezone_set('Asia/Shanghai');

require('./Lingeros/lingeros.php'); //加载初始化文件
require('./Repair/Conf/config.php'); //加载配置文件

lingeros::run($config);