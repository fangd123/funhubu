<?php
namespace Home\Controller;

use Think\Controller;

class ErrorController extends Controller {
	public function notFound(){
		$this->assign('backurl',U('Index/index'));
		$this->display('Home@Public:404');
	}
}
