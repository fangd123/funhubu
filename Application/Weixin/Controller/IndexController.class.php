<?php
namespace Weixin\Controller;
use Think\Controller;

use Weixin\Common\Wechat;

class IndexController extends Controller {
	protected $weObj;

	function __construct(){
		//创建微信对象
		$this->weObj = new Wechat(array(
			'token' => C('TOKEN'),
			'appid' => C('APPID'),
			'appsecret' => C('APPSECRET'),
		));
		$this->weObj->valid();
	}

    public function index(){
		//解析请求
		$msgType = $this->weObj->getRev()->getRevType();
		$openId = (string)$this->weObj->getRevFrom();//防止直接从浏览器访问匹配到所有记录
		
		//判断用户是否被禁用
		$User = D('User');
		if($User->isLocked($openId)){
			$this->reply('');
			return;
		}

		//记录用户信息
		$info = $this->weObj->getUserInfo($openId);
		$data = array(
			'open_id' => $openId,
			'nickname' => $info['nickname'],
			'sex' => $info['sex'],
			'headimg' => $info['headimgurl'],
			'position' => $info['province'].' '.$info['city'],
		);
		$userId = $User->logUser($data);

		//记录请求消息
		$Message = D('Message');
		$data = $this->weObj->getRevData();
		$Message->logMsg($userId, $msgType, $data);
        
		//匹配关键字对应的插件id
		$addonId = $this->getAddon($openId, $msgType);
		
		//获取插件名
		$Addon = D('Addon');
		$addonName = $Addon->checkAddon($openId, $addonId);

		//调用插件返回数据
		$answer = $this->callAddon($addonName);

        //响应请求
		$this->reply($answer);
    }

	/*
	*匹配被调用的插件
	*openId	微信用户id
	*msgType	消息类型
	*return $addon_id int|false
	*/
	private function getAddon($openId, $msgType){
		//根据关键字查找插件id
		$Keyword = D('Keyword');
		return $Keyword->getAddon($msgType, $this->weObj);
	}
	
	/*
	*调用插件返回数据
	*addonName 插件名
	*return array(
	*	'type' => $type,
	*	'data' => $data,
	*	)
	*/
	private function callAddon($addonName){
		if(empty($addonName)){
			return false;
		}
		$arr = explode('/',$addonName);
		try{
			$module = $arr[0] ? trim($arr[0]) : 'Addon';
			$controller = $arr[1] ? trim($arr[1]) : 'Index';
			$method = $arr[2] ? trim($arr[2]) : 'index';

			$Addon = A($module . '/' .$controller);
			if(method_exists($Addon,$method)){
				return $Addon->$method($this->weObj);
			}
		}catch(Exception $e){}
		return false;
	}
	
	/*
	*回复消息
	*answer 消息数组
	*/
	private function reply($answer){
		if(empty($answer)){
			$this->weObj->text(EMPTY_ANSWER)->reply();
			return;
		}
		
		$type = $answer['type'];
		try{
			if(method_exists($this->weObj, $type)){
				$this->weObj->$type($answer['data'])->reply();
				return;
			}
		}catch(Exception $e){}
		
		$this->weObj->text(EMPTY_ANSWER)->reply();
	}

	public function _empty(){
		$this->assign('backurl',U('Home/Index/index'));
		$this->display('Home@Public:404');
	}
}
