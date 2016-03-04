<?php
namespace Addon\Model;

use Think\Model;

class LibSearchModel extends Model {
	protected $fields = array(
		'id',
		'user_id',
		'keyword',
		'update_time',
	);

	public function log($userId, $keyword){
		$data = array(
			'user_id' => $userId,
			'keyword' => $keyword,
		);
		$this->add($data);
	}
}
