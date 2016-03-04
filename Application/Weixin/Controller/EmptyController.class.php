<?php
namespace Weixin\Controller;

use Think\Controller;

class EmptyController extends Controller {

	public function _empty(){
		$this->assign('backurl',U('Home/Index/index'));
		$this->display('Home@Public:404');
	}
}