<?php
namespace Admin\Controller;

use Think\Controller;

class EmptyController extends Controller {

	public function _empty(){
		$this->assign('backurl',U('Admin/Index/index'));
		$this->display('Home@Public:404');
	}
}