<?php
/*
 * 提供视图模板接口
 */

class View{
	public static $view;

	public static function init($view_type, $config)
	{
		static::$view = new $view_type;	//实例化对应的模板类
		foreach($config as $key=>$value)
		{ //配置模板
			static::$view->$key = $value;
			if ($key == 'template_dir'){  //使模板能找到对应控制器名称的文件夹
				$value .= '/'.lingeros::$controller;//不懂是否属于bug，因为在初始化时强制了初始化的顺序
				static::$view->$key = $value;
			}
		}
	}

	public static function assign($data_arr)
	{
		foreach($data_arr as $key=>$value)
		{
			static::$view->assign($key,$value);
		}
	}

	public static function assignArr($var_name,$data_arr)
	{
		static::$view->assign($var_name, $data_arr);
	}

	public static function display($tpl)
	{
		static::$view->display($tpl);
	}
}