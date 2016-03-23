<?php
namespace Addon\Model;

class JwcModel {
	protected $hubu;
	protected $stu;

	function __construct(){
		try{
			$this->hubu = new \Addon\Model\HubuModel();
			$this->stu = D('Stu');
		}catch(Exception $e){
			exit;
		}
	}

	//获取数据
	public function action($userId, $type){
		//选择策略,get请求或者请求的semester在缓存范围内
		if(IS_GET || $this->stored($type)){
			//读数据库
			$model = D($type);
			$func = 'get' . $type;
			$data = $model->$func($userId, I('post.semester'));
			if(IS_GET || $data['status'] == 'success'){
				return $data;
			}
		}
		//模拟登录教务处
		if(empty(I('post.cookie'))){
			return array(
				'status' => 'error',
				'code' => '别闹了',
			);
		}
		$this->hubu->setCookie(I('post.cookie'));
		$this->hubu->validateSession(I('post.cookie'));
		$hasAccount = $this->hasAccount(I('get.open_id'));

		if(!empty(I('post.verify'))){
			$this->login($userId);
		}

		//登录失败，返回错误信息
		$res = $this->hubu->checkStuInfo();
		if($res['status'] !== 'success'){
			if($hasAccount && $res['code'] == '该帐号不存在或密码错误'){
				$this->stu->deleteAccount($userId);
			}
			return $res;
		}

		//登录成功,缓存cookie
		$mc = S(array('type'=>'memcached'));
		$cookie = $mc->set(I('get.open_id') . '_cookie', I('post.cookie'), 0 ,1800);

		//未绑定帐号，记录教务处帐号并对用户进行分组
		if(!$hasAccount){
			$username = I('post.username');
			$password = I('post.password');
			$this->stu->logAccount($userId, $username, $password);
			$this->stu->logInfo($userId, $this->hubu->getClass());
			$group = new \Addon\Model\GroupModel();
			$group->updateGroupMembers($username, I('get.open_id'));
		}else{
			$account = $this->stu->getAccount($userId);
			$username = $account['stu_num'];
			$password = $account['password'];
		}

		//获取数据，录入数据库
		$func = 'get' . $type;
		$raw = $this->hubu->$func(I('post.semester'), $username);
		$model = D($type);
		$data = $model->log($userId, $raw, I('post.semester'));


		//返回数据
		if(empty($data)){
			$data = array(
				'status' => 'error',
				'code' => '系统错误，请稍候再试',
			);
		}
		return $data;
	}

	//微信用户是否绑定教务处帐号
	public function hasAccount($openId){
		$user = D('Weixin/User');
		return $user->hasAccount($openId);
	}

	//从教务从获取验证码
	public function getVerify(){
		return $this->hubu->setCookie(I('get.cookie'))->getVerCodePic();
	}

	//获取cookie，如果存在缓存则获取缓存，否则从教务处获取新的
	public function getCookie(){
		$openId = I('get.open_id');
		$mc = S(array('type'=>'memcached'));
		$cookie = $mc->get($openId . '_cookie');

		if(!empty($cookie) && $this->hubu->validateSession($cookie)){
			return array(
				'data' => $cookie,
				'type' => 'old',
			);
		}
		if(!empty($cookie)){
			$mc = S(array('type'=>'memcached'));
			$mc->delete($openId . '_cookie');
			$mc->delete($openId . '_do');
			$mc->delete($openId . '_data');
		}
		$cookie = $this->hubu->getCookie();
		return array(
			'data' =>$cookie,
			'type' =>'new',
		);
	}

	//显示的学期
	public function getSemester($type){
		$semester = D('Addon/Semester');
		return $semester->getSemester($type);
	}

	//登录到教务处
	private function login($userId){
		if(empty($username = I('post.username')) 
			|| empty($password = I('post.password'))){
			$account = $this->stu->getAccount($userId);
			if(empty($account)){
				return;
			}

			$username = $account['stu_num'];
			$password = $account['password'];
		}

		return $this->hubu->setForm($username, $password, I('verify'))->logon();
	}

	//检查请求的semester是否在缓存范围内
	private function stored($type){
		$semester = I('post.semester');
		if(empty($semester)){
			return false;
		}

		if($semester < $this->currentSemester($type)){
			return true;
		}
		return false;
	}

	//最新待查询的学期
	private function currentSemester($type){
		$semester = D('Addon/Semester');
		return $semester->currentSemester($type);
	}
}
