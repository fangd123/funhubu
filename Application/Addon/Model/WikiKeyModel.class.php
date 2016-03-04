<?php
namespace Addon\Model;

use Think\Model;

class WikiKeyModel extends Model {
	protected $fields = array(
		'id',
		'isAvailable',
		'key',
		'count',
	);

	//获取所有可用的关键字
	public function getKeys(){
		$where = array(
			'isAvailable' => 1,
		);
		return $this->where($where)->getField('id,key');
	}

	//获取关键字对应的id
	public function getKeyId($key){
		$where = array(
			'isAvailable' => 1,
			'key' => array(
				'like',
				'%'.$key.'%',
			),
		);
		$res = $this->where($where)->getField('id,count');
		if(empty($res)){
			return;
		}
		foreach($res as $key => $value){
			$id = $key;
			$count = $value;
		}
		$count += 1;
		$this->where(array('id'=>$id))->save(array('count' => $count));
		return $id;
	}
}
