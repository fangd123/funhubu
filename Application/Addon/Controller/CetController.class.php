<?php
namespace Addon\Controller;

use Think\Controller;

class CetController extends Controller {
	public function index($weObj){
		$openId = $weObj->getRevFrom();
		$mc = S(array('type'=>'memcached'));
		$mc->set($openId . '_do', 'Addon/Cet/getNum', 600);
		return array(
			'type' => 'text',
			'data' => '请输入您的考号（输入"exit"退出）：',
		);
	}
	
	//获取考号
	public function getNum($weObj){
		$openId = $weObj->getRevFrom();
		$data = trim($weObj->getRevContent());
		if(empty($data)){
			return array(
				'type' => 'text',
				'data' => '请输入您的考号:',
			);
		}
		$mc = S(array('type'=>'memcached'));
		$mc->set($openId . '_do', 'Addon/Cet/getName', 600);
		$mc->set($openId . '_data', $data, 600);
		return array(
			'type' => 'text',
			'data' => '请输入您的姓名(前两个字）:',
		);
	}

	//获取姓名,查出成绩
	public function getName($weObj){
		$openId = $weObj->getRevFrom();
		$user = D('Weixin/User');
		$userId = $user->getUserId($openId);
		$name = trim($weObj->getRevContent());
		if(empty($name)){
			return array(
				'type' => 'text',
				'data' => '请输入您的姓名',
			);
		}
		
		$mc = S(array('type'=>'memcached'));
		$num = $mc->get($openId .'_data');

		$Cet = D('Addon/Cet');
		$grade = $Cet->getGrade($openId, $userId, $num, $name);

		$result = '';
		if(is_array($grade)){
			foreach($grade as $key => $value){
				$result .= $key . ':' . $value . "\n";
			}
		}else{
			$result = $grade;
		}

		$mc->rm($openId . '_do');
		$mc->rm($openId . '_data');

		return array(
			'type' => 'text',
			'data' => $result,
		);
	}
}
