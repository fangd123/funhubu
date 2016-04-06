<?php
namespace Addon\Controller;

use Think\Controller;

class BoardController extends Controller {
	//表白墙首页
	public function index(){
		$this->display();
	}

	//读取消息
	public function read(){
		$board = D('board');
		$data = $board->read(I('get.id/d',0));
		$this->ajaxReturn($data);
	}

	//写入消息
	public function write(){
		$board = D('board');
		$openId = I('get.open_id');
		$from = I('post.from','','strip_tags');
		$to = I('post.to','','strip_tags');
		$content = I('post.inputMsg','','strip_tags');
		$data = $board->write($openId, $from, $to, $content);
		$this->ajaxReturn($data);
	}

	//表白墙点击事件处理
	public function click($weObj){
		$openId = $weObj->getRevFrom();
		return array(
			'type' => 'news',
			'data' => array(
				array(
					'Title'=>'与你相约，玩转湖大表白墙',
					'Description'=>"你的忧伤\n眼看就要\n越过眉头\n淹没我的双肩\n我的哀愁\n却无法让你看见\n相信我是爱你的",
					'PicUrl'=>'http://wx.0x2c.cn/Public/img/board.jpg',
					'Url'=>'http://wx.0x2c.cn/Addon/Board?open_id='.$openId,
				),
			),
		);
	}
}
