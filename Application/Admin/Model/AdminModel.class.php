<?php
namespace Admin\Model;

use Think\Model;

class AdminModel extends Model {
	protected $fields = array(
		'id',
		'username',
		'password',
		'update_time',
	);

	public function check(){
		$where = array(
			'username' => I('post.username'),
			'password' => md5(I('post.password')),
		);
		if($data = $this->where($where)->find()){
			$data['update_time'] = date('Y-m-d H:i:s', time());
			$this->save($data);
			return true;
		}
		return false;
	}
}
