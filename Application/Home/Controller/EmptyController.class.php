<?php
namespace Home\Controller;

use Think\Controller;

class EmptyController extends Controller {

	public function _empty(){
		$this->assign('backurl',U('Index/index'));
		$this->display('Public:404');
	}
}