<?php
namespace Weixin\Model;

use Think\Model;

class UserModel extends Model {
	protected $fields = array(
		'id',
		'isAvailable',
		'open_id',
		'nickname',
		'sex',
		'headimg',
		'position',
		'update_time',
	);
	/*
	*记录用户
	*data	array 微信用户信息
	*return int | false 用户id
	*/
	public function logUser($data){
		$where = array('open_id' => $data['open_id']);
		$result = $this->where($where)->find();
		
		//如果不存在或者达到更新期限，则更新数据
		if(empty($result)){
			return $this->add($data);
		}elseif($this->isExpired($result['update_time'])){
				$data['update_time'] = date('Y-m-d H:i:s',time());
				return $this->where($where)->save($data);
		}
		return $result['id'];
	}

	//锁定微信用户
	public function lockUser($openId){
		$where = array('open_id' => $openId);
		$data['isAvailable'] = 0;
		$this->where($where)->save($data);
	}

	//判断微信用户是否被锁定
	public function isLocked($openId){
		$where = array('open_id' => $openId);
		$res = $this->where($where)->getField('isAvailable');
		return ($res === '0');
	}
	
	/*
	*用户信息是否需要更新
	*$time 上次更新时间
	*return bool
	*/
	public function isExpired($time){
		$update_time = strtotime($time);
		$now = time();
		if($update_time + C('EXPIRE_TIME') < $now){
			return true;
		}
		return false;
	}

	//判断微信用户是否绑定教务处帐号
	public function hasAccount($openId){
		if(empty($openId)){
			return false;
		}

		$data = $this->where(array('open_id' => $openId))
			->join('__STU__ ON __USER__.id = __STU__.user_id')->find();

		if(empty($data)){
			return false;
		}
		return true;
	}

	//用户是否关注本公众号
	public function isSubscribed($openId){
		$where = array(
			'open_id' => $openId,
			'isAvailable' => 1,
		);
		$data = $this->where($where)->find();
		return !empty($data); 
	}

	//获取user_id
	public function getUserId($openId){
		$where = array(
			'open_id' => $openId,
			'isAvailable' => 1,
		);
		$data = $this->where($where)->getField('id');
		return empty($data) ? 0 : $data;
	}

}
