<?php
namespace Admin\Controller;

use Think\Controller;

class IndexController extends Controller {
	//后台首页
    public function index(){
		if(!session('?name')){
			$this->redirect('Index/login');
		}
		$this->display();
    }
	
	//后台登录
	public function login(){
		if(session('?name')){
			$this->redirect('Index/index');
		}
		
		if(IS_POST && $enable = $this->checkVerify(I('post.code'))){
			$Admin = D('Admin');
			if($Admin->check()){
				session('name','admin');
				$this->ajaxReturn(array('url' => U('Index/index')));
			}
			$this->ajaxReturn(array('info' => 'error'));
		}elseif(IS_POST && !$enable){
			$this->ajaxReturn(array('info' => 'tip'));
		}

		$this->display();
	}
	
	//注销登录
	public function logout(){
		session('name',null);
		$this->redirect('Index/login');
	}

	//生成验证码
	public function verify(){
		$Verify = new \Think\Verify();
		$Verify->entry();
	}
	
	//检查验证码是否输入正确
	public function checkVerify($code, $id=''){
		$Verify = new \Think\Verify();
		$status = $Verify->check($code, $id);
		return $status;
	}
}
