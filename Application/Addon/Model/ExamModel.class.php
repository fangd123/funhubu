<?php 
namespace Addon\Model;

use Think\Model;

class ExamModel extends Model {
	protected $fields = array(
		'id',
		'user_id',
		'semester',
		'name',
		'start_time',
		'end_time',
		'place',
	);

	public function getExam($userId, $semester){
		$where['user_id'] = $userId;
		if(!empty($semester)){
			$where['semester'] = $semester;
		}else{
			$where['semester'] = $this->getSemester($userId);
		}
		if(!empty($where['semester'])){
			$result = $this->where($where)->select();
		}

		$result = $this->transform($result);
		if(empty($result)){
			$data = array(
				'status' => 'error',
				'code' => '无数据',
			);
		}else{
			$data = array(
				'status' => 'success',
				'data' => $result,
			);
		}
		return $data;	
	}

	public function log($userId, $raw, $semester){
		$result = $this->fetch($raw);
		if(isset($result['status']) && $result['status'] == 'error'){
			return $result;
		}
		$this->logExam($userId, $result, $semester);
		
		$result = $this->transform($result);
		if(empty($result)){
			$data = array(
				'status' => 'error',
				'code' => '无法获取到考试信息',
			);
		}else{
			$data = array(
				'status' => 'success',
				'data' => $result,
			);
		}
		return $data;
	}

	public function fetch($raw){
		$html= str_get_html($raw);
		$table = $html->find('table',3);
		if(empty($table)){
			return array(
				'status' => 'error',
				'code' => '无法查看该学期考试',
			);
		}

		$result = array();
		foreach($table->find('tr') as $row)
		{
			$result[] = array(
				'name' => $row->find('td',2)->plaintext,
				'start_time' => $row->find('td',3)->plaintext,
				'end_time' => $row->find('td',4)->plaintext,
				'place' => $row->find('td',5)->plaintext
			);
		}
		
		if(empty($result)){
			return array(
				'status' => 'error',
				'code' => '无法查看该学期考试',
			);
		}
        return $result;
	}

	public function logExam($userId, $result, $semester){
		if(empty($result)){
			return;
		}
		$where = array('user_id' => $userId, 'semester' => $semester);
		foreach($result as $row){
			$where['name'] = $row['name'];
			if($this->where($where)->find()){
				continue;
			}
			$row['user_id'] = $userId;
			$row['semester'] = $semester;
			$this->add($row);
		}
	}


	private function getSemester($userId){
		$where['user_id'] = $userId;
		$semester = $this->where($where)->order('semester desc')->group('semester')->getField('semester');
		return $semester;
	}

	private function transform($result){
		if(empty($result)){
			return;
		}
		foreach($result as $row){
			unset($row['id']);
			unset($row['user_id']);
			unset($row['semester']);
			$data[] = array_values($row);
		}
		return $data;
	}
}
