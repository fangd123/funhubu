<?php
namespace Addon\Model;

use Think\Model\AdvModel;

class ActionConfModel extends AdvModel {
	protected $fields = array(
		'id',
		'action_id',
		'configure',
	);

	protected $serializeField = array(
		'configure' => array(
			'start_time',
			'end_time',
		),
	);

}
