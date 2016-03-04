<?php
namespace Addon\Model;

use Think\Model;

class GradeModel extends Model {
	protected $fields = array(
		'id',
		'user_id',
		'semester',
		'name',
		'score',
		'category',
		'credit',
	);

	//从数据库读取成绩
	public function getGrade($userId, $semester){
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

		$this->logGrade($userId, $result, $semester);

		$result = $this->transform($result);
		if(empty($result)){
			$data = array(
				'status' => 'error',
				'code' => '无法获取到成绩',
			);
		}else{
			$data = array(
				'status' => 'success',
				'data' => $result,
			);
		}
		return $data;
	}

	private function fetch($raw){
		$html= str_get_html($raw);
		$gradeDetails = array();
		$table = $html->find('table',3);
		if(empty($table)){
			return array(
				'status' => 'error',
				'code' => '无法查看该学期成绩',
			);
		}

		//部分学生的成绩信息中没有分数这一项
		$gradeStr = $html->find('table',2)->find('tr',0)->find('th',5)->plaintext;
		if(strpos($gradeStr, '总成绩') === false){
			return array(
				'status' => 'error',
				'code' => '教务处中没有您的成绩',
			);
		}
		
		foreach($table->find('tr') as $row){
			$gradeDetails[]=array(
				'semester' => $row->find('td',3)->plaintext,
				'name' => $row->find('td',4)->plaintext,
				'score'=> $row->find('td',5)->plaintext,
				'category'=> $row->find('td',8)->plaintext,
				'credit'=> $row->find('td',10)->plaintext
			);
		}
		
		if(empty($gradeDetails)){
			return array(
				'status' => 'error',
				'code' => '无法查看该学期成绩',
			);
		}
		return $gradeDetails;
	}

	private function logGrade($userId, $result, $semester){
		if(empty($result)){
			return;
		}
		$where = array('user_id' => $userId);
		foreach($result as $row){
			$where['semester'] = $row['semester'];
			$where['name'] = $row['name'];
			if($this->where($where)->find()){
				continue;
			}
			$row['user_id'] = $userId;
			$this->add($row);
		}
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

	private function getSemester($userId){
		$semester = $this->where($where)->order('semester desc')->group('semester')->getField('semester');
		return $semester;
	}
}
