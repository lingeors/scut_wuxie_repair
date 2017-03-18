<?php
/*
 * 处理用户提交的电器的信息。
 * @author: lingeros;
 * @time: 2015-10-5 15:14:56;
 */
class indexModel{
	private $data; //提交上来的数据
	private $data_count = 8; //提交字段数，方便后面的判断是否有空字段提交
	private $week;
	private $week_day;//星期数
	private $user_table = 'repair_user_info';
	public $time; //时间；
	public $state; //状态码
	const  START_DAY = 1; //义修报名开启时间，星期一为编码 1
	const  END_DAY = 4;    //义修报名截止时间，星期四编码为4
	const  START_TIME = 0;
	const  END_TIME = 2100; //报名截止时间，默认为21:00;

	function __construct()
	{
		$this->data = $_POST;
		$this->week = get_this_week();
		$this->week_day = get_this_day();
	}

	public function filter()
	{
		array_walk($this->data, function(&$value){
			$value = trim($value);
		});
		if ($this->data['sub'])
		{//删除空字段
			$this->data = array_filter($this->data, function($var){
				if ($var=='')
					return false;
				else
					return true;
			});
		}

		//判断字段是否为空。在js无法使用时起作用
		if (array_key_exists('id', $this->data))
			return true;
		if (array_key_exists('other', $this->data) && count($this->data) != ($this->data_count+1))
			return false; 
		else if (!array_key_exists('other', $this->data) && ($this->data['type'] == 'other' || count($this->data) != $this->data_count))
			return false;
		else 
			return true;
	}

    public function checkExist()
    {//检查本周是否已经提交，检查本周所有电器数是不是已经超过限度
    	$wechat_id = base64_decode($this->data['date']);
    	$count = Db::getCount($this->user_table, "`wechat_id`='$wechat_id' AND `week`=$this->week");

    	if ($count == 0){
    		$count = Db::getCount($this->user_table, "`week`=$this->week");
    		if ($count<10)
    			return 'able';
    		else 
    			return 'other';
    	}else
    		return 'self';
    }

    public function whetherUpdate()
    {//通过隐藏字段的openid和表id判断是否是对已提交信息进行的修改
    	$wechat_id = base64_decode($this->data['date']); 
    	$id = $this->data['id'];
    	if (isset($wechat_id) && isset($id))
    	{
    		$count = Db::getCount($this->user_table, "`wechat_id`='$wechat_id' AND `id`=$id");
    		if ($count == 1){

    			return true;
    		}
    		else {
    			return false;
    		}
    	}
    	return false;
    }

	public function save($update = false) //false时为插入操作，true是为更新操作
	{
		array_pop($this->data); //sub字段出栈;
		if ($this->data['type'] == 'other')
		{ //如果电器类型为其它则将其赋值为else字段的值
			$this->data['type'] = $this->data['other'];
			unset($this->data['other']);
		}
		//添加其他必要字段
		$this->data['state'] = 0; //电器状态值
		$this->data['week'] = get_this_week(); //本周次
		$this->data['apply_time'] = date('Y-m-d', time());
		 
			
		if ($update){
			$wechat_id = base64_decode($this->data['date']); 
    		$id = $this->data['id'];
    		unset($this->data['id']);
			unset($this->data['date']);
			unset($this->data['other']);
			$where  = "id=$id AND wechat_id='$wechat_id'";
			if (Db::update($this->user_table,$this->data, $where))
				return true;
			else
				return false;
		}
		$this->data['wechat_id'] = base64_decode($this->data['date']); //wechat_id解密
		unset($this->data['date']);
		if (Db::insert($this->user_table, $this->data, true))
			return true;
		else 
			return false;
	}

	public function getMessage()
	{//获取当周已经报名但还未处理的人数和以获得通过的人数
		$where = "state=0 AND session_level=15 AND week='$this->week'";
		$count['baoming'] = Db::getCount($this->user_table, $where);
		// $where = "state=2 AND session_level=15 AND week='$this->week'";
		// $count['tongguo'] = Db::getCount($this->user_table, $where);
		return $count;
	}

	public function getByWechat($wechat_id)
	{
		$wechat_id = base64_decode($wechat_id);
		$sql = "SELECT * FROM {$this->user_table} WHERE `wechat_id`='$wechat_id' AND `week`='$this->week' AND `session_level`=15";

		return Db::fetchOne($sql);
	}

	public function modifyMessage()
	{
		
	}
    public function checkTime()
	{//检查是否在报名规定时间
		if ($this->week_day >= static::START_DAY && $this->week_day <= static::END_DAY)//在规定日期
		{
			if ($this->week_day == static::END_DAY) {//如果是星期四
			if (date('Hi', time())<static::END_TIME) //小于截止时间
					return true;
			}else{
		 		return true;
		 	}
		 }
		 return false;
		
	}
}