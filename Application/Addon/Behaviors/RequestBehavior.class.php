<?php
namespace Addon\Behaviors;

use \Think\Behavior;
/*
*记录请求到日志表
*/
class RequestBehavior extends Behavior {
	public function run(&$param) {
		
		$Request = M('Request');

		$data['ip'] = get_client_ip();
		$data['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$data['method'] = $_SERVER['REQUEST_METHOD'];
		$data['uri'] = $_SERVER['REQUEST_URI'];

		if (IS_POST){
			$query = array();
			foreach ($_POST as $key => $value) {
				$query[] = $key .'='. $value;
			}

			$data['query_string'] .= addslashes(implode('&', $query));
		}
		
		$Request->add($data);
	}
}