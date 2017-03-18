<?php

$config = [
	// 'db' =>[
	// 	'dbtype'  => 'mysql',
	// 	'dbhost'  => 'localhost',
	// 	'dbuser'  => 'root',
	// 	'dbpsw'   => 'root',
	// 	'dbname'  => '2015repair',
	// 	'dbcharset' => 'utf8',
	// ],
	'db' =>[
		'dbtype'  => 'mysql',
		'dbhost'  =>  SAE_MYSQL_HOST_M,
		'dbport'  =>  SAE_MYSQL_PORT,
		'dbuser'  =>  SAE_MYSQL_USER,
		'dbpsw'   =>  SAE_MYSQL_PASS,
		'dbname'  =>  SAE_MYSQL_DB,
		'dbcharset' => 'utf8',
	],
	'smarty' => [
		'right_delimiter' => '}>',
		'left_delimiter'  => '<{',
		'template_dir' 	  => APP_PATH.'/'.APP_NAME.'/tpl',
		// 'compile_dir'	  => APP_PATH.'/tmp/templates_c',
		'compile_dir'	  => 'saemc://smartytpl/',
		'cache_dir' 	  => 'saemc://smartytpl/',
		'compile_locking' => false, // 防止调用touch,saemc会自动更新时间，不需要touch
		
	],	

];
