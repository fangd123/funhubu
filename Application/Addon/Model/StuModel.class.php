<?php
namespace Addon\Model;

use Think\Model;

class StuModel extends Model {
	protected $fields = array(
		'id',
		'user_id',
		'stu_num',
		'name',
		'birthday',
		'password',
		'department',
		'class',
	);

	//获取帐号密码
	public function getAccount($userId){
		$data = $this->where(array('user_id' => $userId))->getField('stu_num,password');
		foreach($data as $key => $value){
			return array(
				'stu_num' => $key,
				'password' => $value,
			);
		}
	}

	//删除指定用户绑定的教务处帐号
	public function deleteAccount($userId){
		$data = $this->where(array('user_id' => $userId))->delete();
		$mc = S(array('type'=>'memcached'));
		$mc->delete($openId . '_cookie');
		$mc->delete($openId . '_do');
		$mc->delete($openId . '_data');
	}

	//记录用户名密码
	public function logAccount($userId, $username, $password){
		$data['user_id'] = $userId;
		$data['stu_num'] = $username;
		$data['password'] = $password;
		$this->add($data);
		return $data;
	}

	//记录班级
	public function logInfo($userId, $raw){
		//解析html文本
		try{
			$html= str_get_html($raw);
			$department = $html->find('td[colspan="2"]',1)->plaintext;
			$department = substr($department,9);
			$class = $html->find('td[colspan="2"]',2)->plaintext;
			$class = substr($class,9);
			$table = $html->find('table#xjkpTable',0);
			if(!empty($table)){
				$name = $table->find('tr',3)->find('td',1)->plaintext;
				$name = str_replace('&nbsp;','',$name);
				$birthday = $table->find('tr',4)->find('td',1)->plaintext;
				$birthday = str_replace('&nbsp;','',$birthday);
			}
		}catch(Exception $e){
			return array();
		}

		//写入数据库
		$data = array(
			'name' => $name,
			'birthday' => $birthday,
			'department' => $department,
			'class' => $class,
		);
		$this->where(array('user_id' => $userId))->save($data);

		return $data;
	}
}
