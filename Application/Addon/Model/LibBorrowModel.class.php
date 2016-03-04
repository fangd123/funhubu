<?php
namespace Addon\Model;

use Think\Model;

class LibBorrowModel extends Model {
	protected $fields = array(
		'id',
		'user_id',
		'book_id',
		'borrow_date',
		'return_date',
		'place',
		'update_time',
	);

	//记录历史借阅
	public function log($userId, $result){
		if(!is_array($result)){
			return;
		}
		$data['user_id'] = $userId;
		foreach($result as $row){
			$data['book_id'] = $row['book_id'];
			$data['borrow_date'] = $row['borrow_date'];
			$data['return_date'] = $row['return_date'];
			$data['place'] = $row['place'];
			if($this->where($data)->find()){
				continue;
			}
			$this->fetchSql()->add($data);
		}
	}

	//是否更新借阅记录
	public function needUpdate($userId){
		$where = array('user_id' => $userId);
		$updateTime = $this->where($where)->order('update_time desc')->getField('update_time');

		$updateTime = strtotime($updateTime);
		$now = time();
		$expired = 10 * 24 * 60 * 60;
		if($updateTime + $expired < $now){
			return true;
		}
	}
}
