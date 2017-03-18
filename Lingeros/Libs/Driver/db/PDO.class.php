<?php

/*
 * 封装数据库操作
 * @author: lingeros
 * @create_time: 2015-9-29 12:05:13
 * 安全性不佳，待完善
 */

class DbPdo{

	 //@var save the instance of the PDO object
	 protected static $_instance = null;
	 protected $dsn;
	 protected $dbh;

	/*
	 * @function: 连接数据库
	 * @param: array $config;
	 * @param: string $dbtype; the database type;
	 * @param: string $dbhost; the database host name;
	 * @param: string $dbuser; the user name;
	 * @param: string $dbpsw;  the database password;
	 * @param: string $dbname; the database name;
	 * @param: string $dbcharset; the database charset;
	 */
	private function __construct($config)
	{
		if (!empty($config)){
			extract($config);
			try{
				$this->dsn = $dbtype.':host='.$dbhost.';port='.$dbport.';dbname='.$dbname;  //把连接设置为持久连接
				$this->dbh = new PDO($this->dsn, $dbuser, $dbpsw, array(PDO::ATTR_PERSISTENT => true));
				$this->dbh->query('set names utf8');
				$this->dbh->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);//
			}catch(PDOException $e){
				$this->error($e->getMessage());
			}
		}
		else{
			$this->error('数据库配置信息不能为空！');
		}
		
	} 

	/*
	 *  get the instance of the PDO object;
	 *  @param: array $config;  the database's config;
	 *  @return: object; $_instance;
	 */
	public static function getInstance($config)
	{
		if (static::$_instance === null){
			static::$_instance = new DbPdo($config);
		}
		return static::$_instance;
	}

	/*
	 * exce a sql command;
	 * @param: string $sql; //a sql string;
	 * @result: object $recordset; a result set as a PDOStatement object
	 */
	public function query($sql)
	{
		$recordset = $this->dbh->query($sql);
		if (!$recordset)
		{
			$this->getPDOError();
		}else{
			return $recordset;
		}
	}
	/*SELECT * FROM `post` WHERE 1
	public function query($table, $field, $)
	{
		$recordset = $this->dbh->query($sql);
		
		if (!$recordset)
		{
			$this->getPDOError();
		}else{
			return $recordset;
		}
	}*/

	/*
	 * get an single record;
	 * @param: object $query;
	 * @return: mixed; depend on the query type if succeed and bool false if failed;
	 */
	public function fetchOne($sql)
	{
		$recordset = $this->query($sql);
		$recordset->setFetchMode(PDO::FETCH_ASSOC);
		return $recordset->fetch();
	}

	/*
	 * get all reocrds;
	 * @param: object $query;
	 * @return: mixed; depend on the query type if succeed and bool false if failed;
	 */
	public function fetchAll($sql)
	{	
		$recordset = $this->query($sql);
		$recordset->setFetchMode(PDO::FETCH_ASSOC);
		return $recordset->fetchAll();
	}

	/*
	 *  insert a row;
	 *  @param: string $table;
	 *  @param: array $arr; the key of the array is the field name,
	 *            the values of the array is the field values;
	 *  @param：bool $flag; 是否使用预处理语句
	 *  @result: mixed $result; the rowCount that is affected;
	 */
	public function insert($table, $arr, $flag=false)
	{
		foreach($arr as $key=>$value)
		{
			$keys[] = "`".$key."`";
			$values[] = "'".$value."'"; //不适用预处理语句
			$val[] = $value; //使用预处理语句
			$prepare[] = '?';
		}
		$key = implode(',', $keys);
		if (!$flag)
		{ //不使用预处理语句
			$value = implode(',', $values);
			$sql = "INSERT INTO ".$table."(".$key.") VALUES(".$value.")";
			$result = $this->dbh->exec($sql);
		}else{ //使用预处理语句
			$prepare = implode(',', $prepare);
			$sql = "INSERT INTO ".$table."(".$key.") VALUES(".$prepare.")";
			$pdo_state = $this->dbh->prepare($sql);

			$number = count($values);
			for($i = 0; $i<$number; $i++)
			{ //绑定参数
				$pdo_state->bindParam($i+1, $val[$i]);
			}

			$result = $pdo_state->execute();
		}
		
		if ($result === false){
			$this->getPDOError();
		}
		return $result;
	}

	/*
	 * delete a row from $table;
	 * @param: string $table; table_name;
	 * @param: string $where;
	 * @result: mixed $result; the rowCount that is affected;
	 */
	public function delete($table, $where)
	{
		$sql = "DELETE FROM ".$table." WHERE ".$where;
		$result = $this->dbh->exec($sql);
		if ($result === false){
			getPDOError();
		}
		return $result;
	}

	/*
	 * update
	 * @param: string $table; table_name;
	 * @param: array $arr; the new data;
	 * @param: string $where;
	 */
	public function update($table, $arr, $where)
	{
		foreach($arr as $key=>$value)
		{
			$values[] = "`".$key."`='".$value."'";
		}
		$value = implode(',', $values);
		$sql = "UPDATE ".$table." SET ".$value.' WHERE '.$where;

		$result = $this->dbh->exec($sql);
		if ($result === false){
			$this->getPDOError();
		}
		return $result;
	}

	/* 
	 * get the given row's count;
	 * @param: string $table;
	 * @param: string $field_name;
	 * @param: string $where;
	 * @return: int 
	 */
	public function getCount($table, $where = '', $field_name = '*')
    {
        $strSql = "SELECT COUNT($field_name) FROM $table";
        if ($where != '') $strSql .= " WHERE $where";
        $count = $this->query($strSql)->fetchColumn();
        return $count;
    }

    
	/*
	 * @function：报错，调试用;
	 * @param string $error
	 * 
	 */
	private function error($error)
	{
		if (DEBUG){
			die('程序运行出错，错误原因为：'.$error);
		}else{}
	}

	/*
     * getPDOError 捕获PDO错误信息
     */
    private function getPDOError()
    {
        if ($this->dbh->errorCode() != '00000') {
            $arrayError = $this->dbh->errorInfo();
            $this->error($arrayError[2]);
        }
    }
}  