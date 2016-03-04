<?php
namespace Addon\Controller;

use Think\Controller;

class JwxtController extends Controller {
	public function course(){
		$this->action('course');
	}

	public function grade(){
		$this->action('grade');
	}

	public function exam(){
		$this->action('exam');
	}

	public function verify(){
		$Jwc = new \Addon\Model\JwcModel();
		echo $Jwc->getVerify();
	}

	private function action($type){
		$openId = I('get.open_id');
		$user = D('Weixin/User');
		$userId = $user->getUserId($openId);

		//openid必须在数据库存在
		if(empty($openId) 
			|| !$user->isSubscribed($openId)){
			$this->show('<script>alert("请先关注");window.location.href="'.U('Home/Index/index').'"</script>');
			return false;
		}

		$Jwc = new \Addon\Model\JwcModel();

		//提交表单，获取数据以json格式返回
		if(IS_AJAX){
			$data = $Jwc->action($userId, $type);
			$this->ajaxReturn($data);
		}
		//从数据库读取数据显示在页面上
		$data = $Jwc->action($userId, $type); 
		$this->assign($type, $data);
		
		//显示输入表单
		$hasAccount = $Jwc->hasAccount($openId);
		$this->assign('account',$hasAccount);	//bool

		//学期
		$this->assign('semester',$Jwc->getSemester($type));

		//隐藏字段cookie,及验证码
		$this->assign('cookie',$Jwc->getCookie());	
		
		$this->display($type);
	}
}
