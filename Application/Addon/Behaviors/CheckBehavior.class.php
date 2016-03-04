<?php
namespace Addon\Behaviors;

use \Think\Behavior;

/*
*检查访问的操作是否可用
*/
class CheckBehavior extends Behavior {
	public function run(&$param) {
		$request = MODULE_NAME .'/'. CONTROLLER_NAME .'/'. ACTION_NAME;

		$Action = D('Addon/Action');
		$enable = $Action->setRequest($request)
		  ->getData()->isAvailable()->checkConf();
		if(!$enable){
			$Error = A('Home/Error');
			$Error->notFound();
			exit;
		}
	}
}
