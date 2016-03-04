<?php
namespace Addon\Model;

use Think\Model\RelationModel;

class ActionModel extends RelationModel {
	protected $fields = array(
		'id',
		'name',
		'isAvailable',
		'description',
		'data_src',
	);

	protected $_link = array(
		'ActionConf' => array(
			'mapping_type' => self::HAS_ONE,
			'as_fields' => 'configure',
		)
	);
	
	private $_request;
	private $_data;
	private $_isAvailable;
	
	public function setRequest($request){
		$this->_request = $request;
		return $this;
	}

	public function getData(){
		$where = array(
			'isAvailable' => 1,
			'name' => $this->_request,
		);
		$this->_data = $this->relation(true)->where($where)->find();
		return $this;
	}

	public function isAvailable(){
		$this->_isAvailable = empty($this->_data) ? false : true;
		return $this;
	}

	public function checkConf(){
		if(!$this->_isAvailable){
			return false;
		}
		if(!empty($this->_data) && !checkConfigure(
		  $this->_data['configure'])){
			return false;
		}
		return true;
	}

	
}
