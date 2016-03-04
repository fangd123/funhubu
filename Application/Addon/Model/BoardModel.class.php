<?php
namespace Addon\Model;

use Think\Model;

class BoardModel extends Model {
	protected $fields = array(
		'id',
		'isAvailable',
		'user_id',
		'from',
		'to',
		'content',
		'time',
	);

	//读数据
	public function read($id){
		if($id == -1){
			return;
		}
		if($id === 0){
			$id = $this->order('id desc')->getField('id');
		}

		$count = 10;
		$where = array(
			'id' => array(
				'ELT',
				$id,
			),
			'isAvailable' => 1,
		);
		$data = $this->where($where)->order('id desc')->limit($count)->getField('id,from,to,content,time');
		if(count($data) < $count){
			$result['id'] = -1;
		}else{
			$keys = array_keys($data);
			$result['id'] = array_pop($keys);
		}
		foreach($data as $row){
			foreach($row as $key => $value){
				$tmp[$key] = htmlspecialchars($value);
			}
			$result['data'][] = $tmp;
		}
		return $result;
	}

	public function write($openId, $from, $to, $content){
		$userId = M('User')->where(array('open_id'=>$openId,'isAvailable'=>1))->getField('id');
		if(empty($userId)){
			return(array('status'=>'error','code'=>'请先关注我们'));
		}
		if(empty($content)){
			return(array('status'=>'error','code'=>'请填写内容'));
		}
		$data['user_id'] = $userId;
		$data['content'] = $content;
		if(!empty($from)){
			$data['from'] = $from;
		}
		if(!empty($to)){
			$data['to'] = $to;
		}
		$res = $this->add($data);
		if(empty($res)){
			return(array('status'=>'error','code'=>'系统错误'));
		}
		return array('status' => 'success');
	}
}
