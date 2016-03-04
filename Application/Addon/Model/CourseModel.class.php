<?php
namespace Addon\Model;

use Think\Model;

class CourseModel extends Model {
	protected $fields = array(
		'id',
		'user_id',
		'semester',
		'details',
	);

	public function getCourse($userId, $semester){
		if(!empty($semester)){
			$where['semester'] = $semester;
		}
		$where['user_id'] = $userId;

		$result = $this->where($where)->order('semester desc')->getField('details');

		if(empty($result)){
			$data = array(
				'status' => 'error',
				'code' => '无数据',
			);
		}
		$result = $this->transform(json_decode($result, true));
		if(empty($result)){
			$data = array(
				'status' => 'error',
				'code' => '系统错误',
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
		//从html文本中解析处课程数据
		$result = $this->fetch($raw);
		if(isset($result['status']) && $result['status'] == 'error'){
			return $result;
		}

		$this->logCourse($userId, $result, $semester);
		$this->logCourseInfo($result, $semester);
		
		//处理课表
		$result = $this->transform($result);
		if(empty($result)){
			$data = array(
				'status' => 'error',
				'code' => '系统错误',
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
		// 通过正则表达式获取所有课程字段位置
		$info = array(
			'课表暂未公布，不能课表查看',
			'该学期无课表时间信息',
		);
		foreach($info as $value){
			if(strpos($raw, $value)){
				return array(
					'status' => 'error',
					'code' => $value,
				);
			}
		}

		$html= str_get_html($raw);
		$table = $html->find('table#kbtable',0);

		if(empty($table)){
			return array(
				'status' => 'error',
				'code' => '无法获取到课表',
			);
		}

		//每项数据的位置
		$name = 0;
		$class = 1;
		$teacher = 2;
		$time = 3;
		$place = 4;
		$separator = '<br>';
		//从全校课表抓取到的课表
		if(strpos($raw,'课表数据内容')){
			$teacher = 1;
			$class = 2;
			$separator = '<br/>';
		}

		$courseDetails = array();
		
		for($row = 1; $row < 6; $row++){
			for($day = 1; $day < 8; $day++){
				$details = $table->find('tr',$row)->find('td',$day)->find('div',1)->innertext;
				$details = str_replace(array('&nbsp;','<nobr>'),'',$details);
				$details = strip_tags($details,'<br>');
				$details = str_replace('<br/>','<br>',$details);
				if($details !== ''){//有课程
						$cell = explode('<br>',$details);
						if(count($cell)<10){
							$cell = array(
								'name' =>$cell[$name],
								'class' => (strlen($cell[$class]) > 30)
									? substr($cell[$class],0,strpos($cell[$class],',')).'等'
									: $cell[$class] ,
								'teacher' => $cell[$teacher],
								'time' => $cell[$time],
								'place' => $cell[$place]
							);
						}else{
						//部分课分单双周占同一个位置
							$gap = 5;
							if(strpos($details,'-------')){
								$gap += 1;
							}
							$cell = array(
								'name' => array($cell[$name],$cell[$name+$gap]),
								'class' => array($cell[$class],$cell[$class+$gap]),
								'teacher' => array($cell[$teacher],$cell[$teacher+$gap]),
								'time' => array($cell[$time],$cell[$time+$gap]),
								'place' => array($cell[$place],$cell[$place+$gap])
							);
						}
					}else{
						$cell = '&nbsp;';
					}
					$rowCourse[$day] = $cell;
			}
			$courseDetails[$row] = $rowCourse;
		}

		//如果所有课都为空，则可能出错
		foreach($courseDetails as $value){
			if(is_array($value)){
				return $courseDetails;
			}
		}

		return array(
			'status' => 'error',
			'code' => '没有您的课',
		);
	}

	private function logCourse($userId, $result, $semester){
		if($this->where(array(
			'user_id' => $userId,
			'semester' => $semester,
			))->find()){
			$this->save(array('details' => json_encode($result)));
		}else{
			$data = array(
				'user_id' => $userId,
				'semester' => $semester,
				'details' => json_encode($result),
			);
			$this->add($data);
		}
	}

	private function logCourseInfo($result, $semester){
		$courseInfo = D('CourseInfo');
		$data = array();
		$data['semester'] = $semester;
		foreach($result as $row){
			foreach($row as $cell){
				if(!is_array($cell)){
					continue;
				}
				
				if(!is_array($cell['name'])){
				//单元格中只有一门课程
					foreach($cell as $key => $value){
						$data[$key] = $value;
					}
					$courseInfo->log($data);
				}else{
				//单元格中有两门课程
					$data1 = $data2 = array('semester' => $semester);
					foreach($cell as $key=> $value){
						$data1[$key] = $value[0];
						$data2[$key] = $value[1];
					}
					$courseInfo->log($data1);
					$courseInfo->log($data2);
				}
			}
		}
	}

	//将课表处理成便于便于输出的字符串
	public function transform($info){
		if(empty($info)){
			return;
		}
		$data = array();
		foreach($info as $row){
			$rowCourse = array();
			foreach($row as $cell){
				//两门课
				if(is_array($cell['name'])){
					$part1 = array();
					$part2 = array();
					foreach($cell as $value){
						$part1[] = $value[0];
						$part2[] = $value[1];
					}
					$cell = implode(',', $part1) .'<br/>-----<br/>'. implode(',', $part2);
				}else{
					$cell = implode(',', $cell);
				}
				$rowCourse[] = $cell;
			}
			$data[] = $rowCourse;
		}
		return $data;
	}
}
