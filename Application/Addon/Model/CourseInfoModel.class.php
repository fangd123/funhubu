<?php
namespace Addon\Model;

use Think\Model;

class CourseInfoModel extends Model {
	protected $fields = array(
		'id',
		'teacher',
		'name',
		'class',
		'semester',
		'time',
		'place',
	);

	//记录全校课程
	public function log($info){
		$where = array(
			'teacher' => $info['teacher'],
			'name' => $info['name'],
			'semester' => $info['semester'],
			'time' => $info['time'],
		);
		if($data = $this->where($where)->find()){
			return;
		}
		$this->add($info);

	}
}
