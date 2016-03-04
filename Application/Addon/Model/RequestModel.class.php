<?php
namespace Addon\Model;

use Think\Model;

class RequestModel extends Model
{
	protected $fields = array(
		'id',
		'ip',
		'user_agent',
		'user_system',
		'path',
		'query_string',
		'update_time',
	);

	
}