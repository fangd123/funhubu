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
	);

	//记录历史借阅，按时间进行增量记录
	public function log($userId, $result){
		if(!is_array($result)){
			return;
		}
		$data['user_id'] = $userId;
		//已记录的借阅数
		$records = $this->where($data)->count();
		//总借数
		$counts = count($result);

		//按时间顺序插入未记录的部分
		for($i = $counts - $records - 1; $i >= 0; $i--){
			$data['book_id'] = $result[$i]['book_id'];
			$data['borrow_date'] = $result[$i]['borrow_date'];
			$data['return_date'] = $result[$i]['return_date'];
			$data['place'] = $result[$i]['place'];
			$this->add($data);
		}
	}
}
