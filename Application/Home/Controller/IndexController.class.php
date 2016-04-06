<?php
namespace Home\Controller;
use Think\Controller;
class IndexController extends Controller {

    public function index(){
		$this->show('Hello World!');
    }

    public function _empty() {
    	$this->assign('backurl',U('Index/index'));
		$this->display('Public:404');
    }
}
