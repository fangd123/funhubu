<?php
namespace Weixin\Model;

use Think\Model;

use Weixin\Common\Wechat;

class KeywordModel extends Model {
	protected $fields = array(
		'id',
		'addon_id',
		'keyword',
	);
	
	/*
	*匹配插件
	*msgType 消息类型
	*weObj 微信对象
	*return addonId int | false
	*/
	public function getAddon($msgType, $weObj){
		//根据关键字查找addon_id
		$where = array();
		switch($msgType){
			case Wechat::MSGTYPE_TEXT:
				$where['keyword'] = trim($weObj->getRevContent());
				break;
			case Wechat::MSGTYPE_EVENT:
				$event = $weObj->getRevEvent();
				if($event['event'] != 'CLICK'){
					$where['keyword'] = $event['event'];
				}else{
					$where['keyword'] = $event['key'];
				}
				break;
			case Wechat::MSGTYPE_IMAGE:

			case Wechat::MSGTYPE_LINK:

			case Wechat::MSGTYPE_VOICE:

			case Wechat::MSGTYPE_SHORTVIDEO:

			case Wechat::MSGTYPE_LOCATION:

			default:
				$where['keyword'] = 'commend';
		}
		$data = $this->where($where)->find();
		if(empty($data)){
			return false;	
		}
		return $data['addon_id'];
	}
}
