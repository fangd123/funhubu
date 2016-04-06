<?php
namespace Addon\Controller;

use Think\Controller;

class ExitController extends Controller {
	public function index($weObj){
		//清空数据，如果后期数据增加此处也应该做出相应的改变
		$openId = $weObj->getRevFrom();
		$mc = S(array('type'=>'memcached'));
		$mc->rm($openId . '_do');
		$mc->rm($openId . '_data');

		return array(
			'type' => 'text',
			'data' => '您已退出当前操作，查看其他功能请输入"帮助"',
		);
	}
}
