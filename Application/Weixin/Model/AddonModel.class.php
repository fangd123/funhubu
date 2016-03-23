<?php
namespace Weixin\Model;

use Think\Model\RelationModel;

class AddonModel extends RelationModel {
	protected $fields = array(
		'id',
		'name',
		'isAvailable',
		'description',
		'data_src',
	);

	protected $_link = array(
		'AddonConf' => array(
			'mapping_types' => self::HAS_ONE,
			'as_fields' => 'configure',
			)
	);	
	
	/*
	*检查插件返回插件名
	*addonId 插件id
	*return string | false
	*/
	public function checkAddon($openId, $addonId){
		if($addonId != 1){	//1为退出操作
			$mc = S(array('type'=>'memcached'));
			if($addonName = $mc->get($openId.'_do')){
				return $addonName;
			}
		}

		$data = $this->getData($addonId);
		$enable = $this->checkConf($data['configure']);
		if(!$enable){
			return false;
		}
		return $data['name'];
	}
	
	/*
	*获取插件信息及配置
	*addonId 插件id
	*return array
	*/
	public function getData($addonId){
		$where = array(
			'addon.id' => $addonId,
			'isAvailable' => 1,
		);
		$data = $this->relation(true)->where($where)->find();
		return $data;
	}
	
	/*
	*检查当前环境是否符合配置要求
	*configure 配置项
	*return bool
	*/
	public function checkConf($configure){
		if(empty($configure)){
			return true;
		}
		return checkConfigure($configure);
	}
}
