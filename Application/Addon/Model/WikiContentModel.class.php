<?php
namespace Addon\Model;

use Think\Model;

class WikiContentModel extends Model {
	protected $fields = array(
		'id',
		'isAvailable',
		'key_id',
		'content',
		'title',
		'description',
		'picurl',
		'url',
		'count',
	);

	//获取条目列表
	public function getList(){
		$where = array(
			'isAvailable' => 1,
		);
		return $this->where($where)->getField('id,content');
	}

	//搜索
	public function search($id, $content){
		$where = array(
			'key_id' => $id,
			'isAvailable' => 1,
			'content' => array(
				'like',
				'%'.$content.'%',
			),
		);
		$res = $this->where($where)->select();
		if(empty($res)){
			return '未找到结果';
		}
		foreach($res as $row){
			$id = $row['id'];
			$item['Title'] = $row['title'];
			$item['Description'] = $row['description'];
			$item['PicUrl'] = $row['picurl'];
			$item['Url'] = $row['url'];
			$count = $row['count'] + 1;
			$data[] = $item;
			$this->where(array('id'=>$id))->save(array('count' => $count));
		}
		return $data;
	}
}
