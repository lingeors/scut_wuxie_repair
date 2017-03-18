<?php
/*
 * 前台用户界面控制器
 */

class indexController{
	public function index()
	{
		if (strlen(trim($_GET['c'])) == 40){//在加上微信接口确认已关注就可以唯一确定是从微信公众号而来
			$model = M('index');
			$number = $model->getMessage();//已报名人数
			$number['wechat_id'] = $_GET['c']; //使用c字段存储用户微信openid。
			if ($_GET['d'])//报名id，在modify.html文件定义
			{
				View::assign(array('id'=>$_GET['d']));//该次报名唯一id
				View::assign($model->getByWechat($_GET['c']));
			}
			View::assign($number);
			
			View::display('index.html');
		}else{
			View::display('error.html');
		}

	}

	public function getPersonalInfo()
	{ //获取个人信息
		if (strlen(trim($_GET['c'])) == 40){
			$model = M('index');
			$number = $model->getMessage();//已报名人数
			$info = $model->getByWechat($_GET['c']);//获取其信息
			$info['wechat_id'] = $_GET['c']; //使用c字段存储用户微信id。
			View::assign($number);
			View::assign($info);
			View::display('modify.html');
		}else{
			View::display('error.html');
		}
	}

	// public function 
	public function test(){
		$model = M('index');
		$model->getMessage();
	}

	public function itemInfo()
	{ //获取数据并提交
		$model = M('index');
		$re = $model->checkTime();//是否在规定时间访问	
		if($re){
			$re = $model->filter();
			if (!$re){
				echo "<script>alert('为更好地为您服务，请填写所有相关信息！');window.history.go(-1);</script>";
				exit();
			}
			$re = $model->whetherUpdate();//是否属于更新操作
			if (!$re)
			{
				$re = $model->checkExist();
				if ($re == 'self'){
					echo "对不起，您这星期已经提交过一次，不能再次报名";
				}
				else if($re == 'other')
					echo "对不起，这星期提交电器总数已经超过，请下周再报名。谢谢您的参与！";
				else{
					if ($model->save())
					{
						View::display('success.html');
					}else{
						echo "<script>alert('提交失败（可能原因，网络繁忙，请重试）'); window.history.go(-1);</script>";
					}
				}
			}else{
				if ($model->save(true))//参数为true表示更新操作
					{
						echo '信息修改成功！';
					}else{
						echo "<script>alert('提交失败（可能原因，网络繁忙，请重试）'); window.history.go(-1);</script>";
					}
			}//else	
		}else{//function
			echo "抱歉，如需查询报名情况，请在无协公众号发送“义修预约”或者数字 3 给小R。小R竭诚为您服务！<br/>
			温馨提示：修改信息只能在管理员审核之前，此时审核已经开始，带来的不便，请见谅！";
   		 }//if 
	}
	private function showMessage($message, $location)
	{
		echo "<script>alert('$message'); window.location.href='$location';</script>";
		exit();
	}

}