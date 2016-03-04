<?php
namespace Addon\Controller;

use Think\Controller;

class JwcController extends Controller {
	//查课表
	public function course($weObj){
		$openId = $weObj->getRevFrom();
		$target = U('Addon/Jwxt/course') .'?open_id='. $openId;
		return array(
			'type' => 'text',
			'data' => '<a href="http://'. $_SERVER['HTTP_HOST'] .$target .'">进入查询界面</a>',
		);
	}
	
	//查成绩
	public function grade($weObj){
		$openId = $weObj->getRevFrom();
		$target = U('Addon/Jwxt/grade') .'?open_id='. $openId;
		return array(
			'type' => 'text',
			'data' => '<a href="http://'. $_SERVER['HTTP_HOST'] .$target .'">进入查询界面</a>',
		);
	}
	
	//查考试安排
	public function exam($weObj){
		$openId = $weObj->getRevFrom();
		$target = U('Addon/Jwxt/exam') .'?open_id='. $openId;
		return array(
			'type' => 'text',
			'data' => '<a href="http://'. $_SERVER['HTTP_HOST'] .$target .'">进入查询界面</a>',
		);
	}
}
