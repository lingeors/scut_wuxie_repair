<?php

function C($name, $method) //原则上不允许控制器方法有自己的参数，所以可以把方法封装
{
	require_once(APP_PATH.'/'.APP_NAME.'/Libs/Controller/'.$name.'Controller.class.php');
	eval('$obj = new '.$name.'Controller();$obj->'.$method.'();');	
}

function M($name) //模型的方法有自己的参数，所以一般不封装，更加灵活
{
	require_once(APP_PATH.'/'.APP_NAME.'/Libs/Model/'.$name.'Model.class.php');
	eval('$obj = new '.$name.'Model();');	
	return $obj;
}

function V($name)
{
	require_once(APP_PATH.'/'.APP_NAME.'/Libs/View/'.$name.'View.class.php');
	eval('$obj = new '.$name.'View();');	
	return $obj;
}

function newAddslashes($str)
{
	return (!get_magic_quotes_gpc()) ? 	addslashes($str) : $str;
}

function get_this_week()
{//获取当前周次
	$base_date = 275; //以2015,10,3为准，今年的天数
	$base_week = 5;//第一学期第五周为准
	$date= date('z', time()); 
	$week = floor(($date-$base_date-1)/7); 
	if ($week>=0){ 
		$week += $base_week;
	}else $week += ($base_week+1);
	
	return $week;
}


function get_this_day()
{//获取当前星期
	return date('w', time());
}

/*
 * 分页函数
 * @param: it $count; 总记录数;
 * @param: int $get; $_GET获得的url中的当前页数
 * @param: int $each_page_count; 每页记录数;
 * @param: string $sql 查询的sql语句; //设置为数组参数可能更佳
 * @return: mixed $re; 存在记录时为记录数组。有无记录都有两个字段，key为prev存储前一页链接。
 * 						key为next存储后一页链接；
 */
function pagination($count, $get, $each_page_count, $sql)
{
	$current_page = isset($get) ? $get : 1; //默认第一页为主页
	$current_page = is_numeric($current_page) ? $current_page : 1; //简单验证
	$current_page = $current_page > 0 ? $current_page : 1;
	$page_count = ceil($count/$each_page_count);//总页数
	$current_page = $current_page < $page_count ? $current_page : $page_count; //默认大于的时候为最后一页
	$url = $_SERVER['REQUEST_URI'];
	$path = parse_url($url);

	$has_show_count = $each_page_count*($current_page-1);
	$uri = $path['path']."?"; //URI

	if ($has_show_count<$count)
	{//还有剩余记录
		$sql = $sql." limit $has_show_count,$each_page_count";
		$re = Db::fetchAll($sql);
		if (strpos($path['query'], 'page=') === false){ //url不存在page参数
			$query = $path['query'].'&page=1';
			$re[0]['next'] = $uri.$query;
		}else if ($page_count > $current_page){ //未到尾页
			$query = preg_replace('/(?<=page=)(.*?)(?=&)|(?<=page=)(.*)/', $current_page+1, $path['query']);
			$re[0]['next'] = $uri.$query;
		}else $re[0]['next'] = '#';
	}else{
		$re['next'] = '#';
	}

	if ($current_page == 1 || $page_count < $current_page)
		$re[0]['prev'] = '#';
	else{
		$query = preg_replace('/(?<=page=)(.*?)(?=&)|(?<=page=)(.*)/', $current_page-1, $path['query']);
		$re[0]['prev'] = $uri.$query;
	}
	$re[0]['count'] = $count;
	$re[0]['page_count'] = $page_count;
	return $re;
}