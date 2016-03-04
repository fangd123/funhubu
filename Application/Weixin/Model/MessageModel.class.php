<?php
namespace Weixin\Model;

use Think\Model\RelationModel;

class MessageModel extends RelationModel {
	protected $fields = array(
		'id',
		'user_id',
		'type',
		'data',
		'update_time',
	);

	 protected $_link = array(
		'User' => array(
			'mapping_type' => self::BELONGS_TO,
		)
	);
	
	/*
	*记录微信消息
	*openId 微信用户id
	*msgType 消息类型
	*data array 消息数据
	*return int | false
	*/
	public function logMsg($userId, $msgType, $data){
		if(empty($userId)){
			return false;
		}
		$data = array(
			'user_id' => $userId,
			'type' => $msgType,
			'data' => json_encode($data),
		);
		return $this->add($data);
	}
 }
