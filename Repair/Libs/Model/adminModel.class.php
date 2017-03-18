<?php
/*
 * 后台信息处理
 */
// 状态码；0 已提交，待审核； 1. 不通过
// 2. 审核通过，待处理 3.维修成功，待返还 
// 4. 维修失败，待返还 5 维修失败，已返还 6 维修成功，已返还


class adminModel{
	private $week;
	private $session_level=15;

	const WAIT_FOR_AUDITING = 0;  //0 已提交，待审核
	const AUDIT_FAIL = 1; 
	const WAIT_FOR_REPAIR = 2; 
	const SUCCESS_WAIT_FOR_RETURN = 3;
	const FAIL_WAIT_FOR_RETURN = 4;
	const FAIL_HAS_RETURN = 5;
	const SUCCESS_HAS_RETURN = 6;

	function __construct()
	{
		$this->week = get_this_week();
	}

	public function getItemInfo($flag)
	{//获取报修记录
		$each_page_count = 5; //每页页数
		if ($flag == 0)
		{//本周待处理
			$where = "`state` != ".static::FAIL_HAS_RETURN.
				 " AND `state` !=".static::SUCCESS_HAS_RETURN.
				 " AND `state` !=".static::AUDIT_FAIL.
				 " AND `week`=$this->week AND `session_level`=$this->session_level";
		}else if($flag==1){//本周所有
			$where = "`week`=$this->week AND `session_level`=$this->session_level";
		}else{//历史所有
			$where = "`session_level`='$this->session_level' ORDER BY `id` DESC";
		}
		$sql = "SELECT * FROM `repair_user_info` WHERE ".$where;
		$count = Db::getCount('repair_user_info', $where); //
		if ($count == 0) //无任何记录
			return false;
		$recordset =  pagination($count, $_GET['page'], $each_page_count, $sql);
		return $this->stateCodeConvert($recordset);
	}	

	

	public function getAdminInfo()
	{//获取所有管理员信息并对数据进行整理
		$sql = "SELECT * FROM `15repair_admin` WHERE `session_level`='$this->session_level'";
		$re = Db::fetchAll($sql);
		foreach($re as $key=>$value)
		{
			$time = explode(' ', $value['operate_time']);
			if (in_array($this->week, $time))
				$re[$key]['on'] = '是';
			else $re[$key]['on'] = '否';
			if ($value['mobile_short_number'] == 0)
				$re[$key]['mobile_short_number'] = '无短号';
			$re[$key]['operate_time'] = '第'.$time[0].'周 and 第'.$time[1].'周';
		}
		return $re;
	}


	public function changeState($data)
	{//管理员操作
		switch($data[0])
		{
			case 01:
				$data_arr = [['state'=>2], '审核通过，待处理'];
				break;
			case 02:
				$data_arr = [['state'=>1, 'remarks'=>$data[2]], '审核不通过'];
				break;
			case 21:
				$data_arr = [['state'=>3], '维修成功，待返还'];
				break;
			case 22:
				$data_arr = [['state'=>4, 'remarks'=>$data[2]], '维修失败，待返还'];
				break;
			case 31:
				$data_arr = [['state'=>6], '维修成功，已返还'];
				break;
			case 41:
				$data_arr = [['state'=>5], '维修成功，已返还'];
				break;
			default:
				break;
		}
		$id = $data[1];
		$where = "`id`=$id";
		if (!Db::update('repair_user_info', $data_arr[0], $where))
			return "fail";
		else{
			return $data_arr[1];
		} 

	}

	private function stateCodeConvert($recordset)
	{
		foreach($recordset as $key => $value)
		{	
			switch($value['state'])
			{
				//创建新字段，因为状态码在后续操作需要用到
				case static::WAIT_FOR_AUDITING: 
					$recordset[$key]['state_str'] = '待审核';
					break;
				case static::AUDIT_FAIL:
					$recordset[$key]['state_str'] = '审核不通过';
					break;
				case static::WAIT_FOR_REPAIR:
					$recordset[$key]['state_str'] = '审核通过，待处理';
					break;
				case static::SUCCESS_WAIT_FOR_RETURN:
					$recordset[$key]['state_str'] = '维修成功，待返还';
					break;
				case static::FAIL_WAIT_FOR_RETURN:
					$recordset[$key]['state_str'] = '维修失败，待返还';
					break;
				case static::FAIL_HAS_RETURN:
					$recordset[$key]['state_str'] = '维修失败，已返还';
					break;
				case static::SUCCESS_HAS_RETURN:
					$recordset[$key]['state_str'] = '维修成功，已返还';					break;
				default:
				 	break;
			}	
		}//foreach	
		return $recordset;
	}

}

// 状态码；0 已提交，待审核； 1. 审核不通过 
// 2. 审核通过，待处理 3.维修成功，待返还 
// 4. 维修失败，待返还 5 已返还


//  <{if $item[rn].state == 0}>
/*      <a class="operate" opcode="01">通过</a>
     <a class="operate" opcode="02">不通过</a>
<{elseif $item[rn].state == 1}>
      <a class="operate" opcode="11">已返还</a>
<{elseif $item[rn].state == 2}>
      <a class="operate" opcode="21">维修成功</a>
      <a class="operate" opcode="22">维修失败</a>
<{elseif $item[rn].state == 3}>
      <a class="operate" opcode="31">已返还</a>
<{elseif $item[rn].state == 4}>
      <a class="operate" opcode="41">已返还</a>
<{/if}>*/