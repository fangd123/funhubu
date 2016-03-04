<?php
namespace Addon\Model;

use Think\Model;

class SemesterModel extends Model {
	protected $fields = array(
		'id',
		'type',
		'semester',
		'isCurrent',
	);

	//获取学期列表
	public function getSemester($type){
		$where = array('type' => $type);
		$data = $this->where($where)->select();
		$result = array();
		foreach($data as $row){
			$key = $row['semester'];
			$value = (int)$row['iscurrent'];
			$result[$key] = $value;
		}
		return $result;
	}

	//获取最新待查询学期
	public function currentSemester($type){
		$where = array(
			'type' => $type,
			'isCurrent' => 1,
		);
		$data = $this->where($where)->getField('semester');
		return $data;
	}
}
