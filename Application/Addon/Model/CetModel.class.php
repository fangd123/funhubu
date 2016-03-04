<?php
namespace Addon\Model;

use Think\Model;

class CetModel extends Model {
	protected $fields = array(
		'id',
		'user_id',
		'type',
		'num',
		'name',
		'score',
		'season',
	);

	/*
	*获取四六级成绩
	*num 准考证号
	*name 姓名
	*return 成绩 array
	*/
	public function getGrade($openId, $userId, $num, $name){
		$season = $this->getSeason();
		$where = array(
			'num' => $num,
			'name' => $name,
			'season' => $season,
		);
		$result = $this->where($where)->find();
		if($result){
			return json_decode($result['score'],true);
		}

		$grade = $this->fetchGrade($num, $name);
		if(empty($grade)){
			return array('温馨提示：暂无结果');
		}
		if(!is_array($grade)){
			return $grade;
		}

		$data = array(
			'user_id' => $userId,
			'type' => $grade['type'],
			'num' => $num,
			'name' => $name,
			'score' => json_encode($grade['data']),
			'season' => $season,
		);
		$this->add($data);
		return $grade['data'];

	}
	
	/*
	*获取考试时间
	*return Y-(6|12)
	*/
	private function getSeason(){
		$year = date('Y', time());
		$month = date('m', time());
		if($month > 6){
			$month = 6;
		}else{
			$month = 12;
			$year -= 1;
		}
		return $year .'-'. $month;
	}

	/*
	*从网页抓取成绩
	*num 准考证号
	*name 姓名
	*return 成绩 array
	*/
	public function fetchGrade($num, $name){
		$snoopy = new \Addon\Common\Snoopy();
		$snoopy->referer = 'http://cet.99sushe.com/';
		$url = 'http://cet.99sushe.com/find';
		$form['id']=$num;
		$form['name']=iconv("UTF-8","gbk//TRANSLIT",$name);
		$snoopy->submit($url,$form);

		$result = iconv('GBK', 'UTF-8', $snoopy->results);
		$result = explode(',',$result);
		if(count($result) < 4){
			return '考号或姓名错误';
		}

		$data['type'] = $result[0];
		$data['data'] = array(
			'listening' => $result[1],
			'reading' => $result[2],
			'writing' => $result[3],
			'total' => $result[4],
		);
		return $data;
	}
}
