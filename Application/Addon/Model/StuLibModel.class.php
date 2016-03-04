<?php
namespace Addon\Model;

use Think\Model;

class StuLibModel extends Model {
	protected $fields = array(
		'id',
		'user_id',
		'stu_num',
		'password',
	);

	//是否绑定图书馆帐号
	public function hasAccount($userId){
		$where = array('user_id' => $userId);
		$data = $this->where($where)->find();
		return !empty($data);

	}

	//记录帐号
	public function logAccount($userId, $stuNum, $password){
		$result = $this->where(array('user_id'=>$userId))->find();
		$data = array(
			'user_id' => $userId,
			'stu_num' => $stuNum,
			'password' => $password,
		);
		if(empty($result)){
		//如果帐号不存在则插入
			$this->add($data);
		}else{
		//帐号存在则更新
			$this->save($data);
		}
	}

	//读帐号密码
	public function getAccount($userId){
		$result = $this->where(array('user_id'=>$userId))->find();
		$data = array(
			'username' => $result['stu_num'],
			'password' => $result['password'],
		);
		return $data;
	}

}
