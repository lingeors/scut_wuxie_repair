<?php
/*
 * 微型模板初始化文件
 */
require('include.list.php');
define('PATH', dirname(__FILE__));

foreach($include_files as $include_file)
{
	require_once(PATH.'/'.$include_file);
}

class lingeros{
	public static $controller;
	public static $config;
	public static $method;
	public static $view;

	public static function initDb()
	{
		Db::init(static::$config['db']);
	}

	public static function initController()
	{
		static::$controller = isset($_GET['m']) ? newAddslashes($_GET['m']) : 'index';
	}

	public static function initMethod()
	{
		static::$method = isset($_GET['a']) ? newAddslashes($_GET['a']) : 'index';
	}

	public static function initView()
	{
		static::$view = View::init('Smarty', static::$config['smarty']);
	}

	public static function run($config)
	{
		static::$config = $config;
		static::initDb();	
		static::initController();
		static::initMethod();
		static::initView(); //需要放在后面，tpl里面才能根据控制器选择对应的文件夹，虽然不知道这样做好不好
		C(static::$controller, static::$method);
	}
}
