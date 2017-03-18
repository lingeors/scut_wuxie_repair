<?php

class adminController{
	public $week;

	public function __construct()
	{
		session_start();
		if ((!isset($_SESSION['auth']) || $_SESSION['auth'] != md5('lingeros')) && lingeros::$method != 'login'){
			$this->showWanring('您尚未登录，请先登陆！', 'admin.php?m=admin&a=login');
		}
	}

	public function modifyInfo()
	{//修改个人信息，暂无需求，以后完善
		View::display('admin_info_modify.html');
	}

	public function adminHistoryInfo()
	{//获取管理员信息
		$model = M('admin');
		$admin_info = $model->getAdminInfo();
		View::assignArr('admin_infos', $admin_info);
		View::display('admin_info.html');
	}

	public function changeState()
	{	//管理员操作，改变报修电器状态。使用异步发送数据方式，视图的异步更新待完善
		$model = M('admin');
		$return_data = $model->changeState($_GET['data']);
		echo $return_data;	
	}

	public function index()
	{ //获取报修记录
		$model = M('admin');
		if ($_GET['flag'] == 2)	//历史所有
			$recordset = $model->getItemInfo(2); 
		else if($_GET['flag'] == 1)//本周所有
			$recordset = $model->getItemInfo(1); 
		else //本周待处理
			$recordset = $model->getItemInfo(0); 
		if (!$recordset) //无任何记录
			View::assign(array('error'=>'right'));
		else{
			View::assign(array('prev'=>$recordset[0]['prev'])); //前一页链接
			View::assign(array('next'=>$recordset[0]['next'])); //下一页链接
			View::assign(array('count'=>$recordset[0]['count'])); //总记录数
			View::assign(array('pagecount'=>$recordset[0]['page_count'])); //总页数

			unset($recordset['prev']);
			unset($recordset['next']);
			unset($recordset['count']);
			unset($recordset['page_count']);

			if ($_GET['flag'] == 0)	{
				View::assign(array('title'=>'本周待处理'));
				View::assign(array('content'=>'本周待处理')); 
			}
			else if($_GET['flag'] == 1){
				View::assign(array('title'=>'本周所有'));
				View::assign(array('content'=>'本周所有'));
			}
			else{
				View::assign(array('title'=>'历史所有')); 
				View::assign(array('content'=>'历史所有')); 
			}
			
			View::assignArr('item', $recordset);
		}
		View::display('index.html');
	}

	public function login()
	{ //登陆
		if (!$_POST){
			View::display('login.html');
		}else{
			$this->checkLogin($_POST);
		}
	}

    private function checkLogin($post_info)
	{  //按维修部要求简单检查登陆信息
		if ($post_info['user'] == 'repair' && $post_info['pwd'] == 'xinxibuzs')
		{
			$_SESSION['auth'] = md5('lingeros');
			echo "<script>window.location.href='admin.php?m=admin';</script>";
		}else{
			echo "<script>alert('账号密码有误'); window.history.go(-1);</script>";
			exit();
		}
	}

	// private function 
	private function showWanring($message, $location)
	{
		echo "<script>alert('$message'); window.location.href='$location';</script>";
		exit();
	}


	public function delete()
	{//调试用，删除提交数据.仅在调试时开启
		if (DEBUG){
			$model = M('admin');
			$recordset = $model->getItemInfo(0);;
		
			if (!$recordset) //无任何记录
				View::assign(array('error'=>'right'));
			else{
				View::assign(array('prev'=>$recordset[0]['prev'])); //前一页链接
				View::assign(array('next'=>$recordset[0]['next'])); //下一页链接
				View::assign(array('count'=>$recordset[0]['count'])); //总记录数
				View::assign(array('pagecount'=>$recordset[0]['page_count'])); //总页数

				unset($recordset['prev']);
				unset($recordset['next']);
				unset($recordset['count']);
				unset($recordset['page_count']);

				View::assign(array('title'=>'本周待处理'));
				View::assign(array('content'=>'本周待处理')); 

				View::assignArr('item', $recordset);
			}
			View::display('deletefortest.html');
		}else{}
	}
	public function deleteInfo()
	{
		if (DEBUG)
		{
			$id = $_GET['what'];
			$where = "`id`=$id";
			Db::delete('repair_user_info', $where);
			echo '';
		}else{}
	}
}
