<?php
namespace Addon\Controller;

use Think\Controller;

class HelpController extends Controller {
	//帮助信息
	public function index(){
		return array(
			'type' => 'text',
			'data' => '帮助信息',
		);
	}

}
