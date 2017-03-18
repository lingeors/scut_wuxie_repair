<?php
/*
 * 使用工厂模式实例化对应的类。
 * 实现一个标准化的接口	
 */
class Db{
	public static  $db;

	public static function init($config)
	{
 		self::$db = DbPdo::getInstance($config); 
	}

	public static function query($sql)
	{
		return self::$db->query($sql);
	}

	public static function fetchOne($sql)
	{
		return self::$db->fetchOne($sql);
	}

	public static function fetchAll($sql)
	{
		return self::$db->fetchAll($sql);
	}

	public static function insert($table, $arr, $flag=false)
	{
		return self::$db->insert($table, $arr);
	}

	public static function delete($table, $where)
	{
		return self::$db->delete($table, $where);
	}

	public static function update($table, $arr, $where)
	{
		return self::$db->update($table, $arr, $where);
	}

	public static function getCount($table, $where = '', $field_name = '*')
    {
    	return self::$db->getCount($table, $where, $field_name);
    }
}
