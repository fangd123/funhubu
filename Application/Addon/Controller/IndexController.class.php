<?php
namespace Addon\Controller;
use Think\Controller;

class IndexController extends Controller {
    public function index(){
        return array(
			'type' => 'text',
			'data' => 'something error!',
		);
    }
}
