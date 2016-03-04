<?php
namespace Addon\Controller;

use Think\Controller;

class LibController extends Controller {
	//查图书
	public function book($weObj){
		$openId = $weObj->getRevFrom();
		$user = D('Weixin/User');
		$userId = $user->getUserId($openId);
		//检查是否绑定帐号
		if($result = $this->hasAccount($userId,$openId)){
			return $result;
		}

		$mc = memcache_init();
		$mc->set($openId .'_do', 'Addon/Lib/searchBook', 0 , 600);
		return array(
			'type' => 'text',
			'data' => '请输入书名（退出请输入“exit”）：',
		);
	}

	//根据关键字查图书
	public function searchBook($weObj){
		$openId = $weObj->getRevFrom();
		$user = D('Weixin/User');
		$userId = $user->getUserId($openId);
		$keyword = trim($weObj->getRevContent());
		if(empty($keyword)){
			$mc = memcache_init();
			$mc->delete($openId .'_do');
			return array(
				'type' => 'text',
				'data' => '操作失败',
			);
		}

		//记录关键字
		$libSearch = D('Addon/LibSearch');
		$libSearch->log($userId, $keyword);

		//根据关键字查询图书
		$lib = new \Addon\Model\LibModel();
		return $lib->searchBooks($keyword);
	}

	//查借阅
	public function borrow($weObj){
		$openId = $weObj->getRevFrom();
		$user = D('Weixin/User');
		$userId = $user->getUserId($openId);
		//检查是否绑定帐号
		if($result = $this->hasAccount($userId,$openId)){
			return $result;
		}
		
		//获取帐号
		$lib = new \Addon\Model\LibModel();
		$lib->getAccount($userId);

		//登录
		$status = $lib->login()->checkStatus();

		//失败处理
		if($status == 'error'){
			$mc = memcache_init();
			$mc->set($openId.'_do','Addon/Lib/bind', 0, 600);
			return array(
				'type' => 'text',
				'data' => '您的帐号已经过期，请重新绑定(学号+密码,默认密码为学号,如20150001+20150001):',
			);
		}elseif($status == 'validate'){
			$mc = memcache_init();
			$mc->set($openId.'_do','Addon/Lib/validate', 0, 600);
			return array(
				'type' => 'text',
				'data' => '为了保证您的帐号安全，请输入姓名验证:',
			);
		}

		//每隔一段时间获取一次历史查询
		$libBorrow = D('Addon/LibBorrow');
		if($libBorrow->needUpdate($userId)){
			$result = $lib->getBorrowList();
			$libBorrow->log($userId, $result);
		}

		//返回借阅数据
		return $lib->getBookRemind();
	}

	//绑定帐号
	public function bind($weObj){
		//获取帐号密码
		$openId = $weObj->getRevFrom();
		$user = D('Weixin/User');
		$userId = $user->getUserId($openId);
		$info = trim($weObj->getRevContent());
		if(strpos($info, '+') == false || count(explode('+',$info)) != 2){
			return array(
				'type' => 'text',
				'data' => '别闹！请重新输入(学号+密码）:',
			);
		}

		$info = explode('+', $info);
		$username = $info[0];
		$password = $info[1];

		//校验
		$lib = new \Addon\Model\LibModel();
		$status = $lib->setAccount($username, $password)->login()->checkStatus();

		//帐号密码错误
		if($status == 'error'){
			return array(
				'type' => 'text',
				'data' => '帐号或密码错误，请重新输入:',
			);
		}

		$stuLib = D('Addon/StuLib');
		$stuLib->logAccount($userId, $username, $password);

		//帐号密码正确，但需要验证
		if($status == 'validate'){
			$mc = memcache_init();
			$mc->set($openId.'_do' ,'Addon/Lib/validate', 0 ,600);
			return array(
				'type' => 'text',
				'data' => '为了保证您的帐号安全，请输入姓名验证:',
			);
		}

		$mc = memcache_init();
		$mc->delete($openId .'_do');
	
		return array(
			'type' => 'text',
			'data' => '帐号绑定成功',
		);	
	}

	//验证帐号
	public function validate($weObj){
		//获取名字
		$openId = $weObj->getRevFrom();
		$user = D('Weixin/User');
		$userId = $user->getUserId($openId);
		$name = trim($weObj->getRevContent());
		if(empty($name)){
			return array(
				'type' => 'text',
				'data' => '请输入姓名:',
			);
		}

		//验证
		$lib = new \Addon\Model\LibModel();
		$lib->getAccount($userId);

		$lib->login()->checkStatus();
		$status = $lib->validate($name);

		//判断
		if(!$status){
			return array(
				'type' => 'text',
				'data' => '验证出错，请重新输入',
			);
		}

		$mc = memcache_init();
		$mc->delete($openId .'_do');
		return array(
			'type' => 'text',
			'data' => '验证成功',
		);
	}

	//图书续借
	public function renew($cookie, $booknum){
		$snoopy = new \Addon\Common\Snoopy();
		$url='http://59.68.64.61:8080/reader/ajax_renew.php?bar_code='.$booknum.'&time='.time();
		$snoopy->cookies["PHPSESSID"]=$cookie;
		$snoopy->fetch($url);
		$this->assign('result',$snoopy->results);
		$this->display();
	}

	//检查是否绑定帐号
	private function hasAccount($userId,$openId){
		$stuLib = D('Addon/StuLib');
		$hasAccount = $stuLib->hasAccount($userId);
		if(!$hasAccount){
			$mc = memcache_init();
			$mc->set($openId . '_do', 'Addon/Lib/bind', 0, 600);
			return array(
				'type' => 'text',
				'data' => ' 请绑定图书馆帐号,格式：帐号+密码,如：20150001+123456',
			);
		}
	}

}
