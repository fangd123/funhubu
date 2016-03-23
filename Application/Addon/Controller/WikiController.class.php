<?php
namespace Addon\Controller;

use Think\Controller;

class WikiController extends Controller {
	//wiki功能入口
	public function index($weObj){
		$openId = $weObj->getRevFrom();
		$wikiKey = D('Addon/WikiKey');
		$keys = $wikiKey->getKeys();
		if(!empty($keys)){
			$data = "请从以下领域中选择(输入exit退出):\n";
			$data .= implode("\n",$keys);
			$mc = S(array('type'=>'memcached'));
			$mc->set($openId.'_do' ,'Addon/Wiki/key', 0, 600);
		}else{
			$data = '此功能暂时无法使用';
		}
		return array(
			'type' => 'text',
			'data' => $data,
		);
	}

	public function key($weObj){
		$openId = $weObj->getRevFrom();
		$key = trim($weObj->getRevContent());
		$mc = S(array('type'=>'memcached'));
		if(empty($key)){
			$mc->delete($openId.'_do');
			return array(
				'type' => 'text',
				'data' => '操作失败',
			);
		}

		$wikiKey = D('Addon/WikiKey');
		$id = $wikiKey->getKeyId($key);
		if(empty($id)){
			$mc->delete($openId.'_do');
			return array(
				'type' => 'text',
				'data' => '不存在该项',
			);
		}
		$wikiContent = D('Addon/WikiContent');
		$list = $wikiContent->getList();
		if(empty($list)){
			$mc->delete($openId.'_do');
			return array(
				'type' => 'text',
				'data' => '该分类下无数据',
			);
		}
		$mc->set($openId.'_data', $id, 0, 600);
		$mc->set($openId.'_do', 'Addon/Wiki/search', 0 , 600);
		$data = "从以下列表中选择(输入exit退出):\n";
		$data .= implode("\n", $list);
		return array(
			'type' => 'text',
			'data' => $data,
		);
	}

	//根据关键字查询并返回图文
	public function search($weObj){
		$openId = $weObj->getRevFrom();
		$content = trim($weObj->getRevContent());
		$mc = S(array('type'=>'memcached'));
		$id = $mc->get($openId.'_data');
		$wikiContent = D('Addon/WikiContent');
		$data = $wikiContent->search($id, $content);
		if(is_array($data)){
			return array(
				'type' => 'news',
				'data' => $data,
			);
		}
		return array(
			'type' => 'text',
			'data' => $data,
		);
	}
}
