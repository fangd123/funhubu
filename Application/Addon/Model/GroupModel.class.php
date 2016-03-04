<?php
namespace Addon\Model;

/**
 * Class Group用户分组
 */
class GroupModel
{
    private $_groupList; 
    private $_weObj;

    public function __construct(){

        $this->_weObj = new \Weixin\Common\Wechat(array(
			'token' => C('TOKEN'),
			'appid' => C('APPID'),
			'appsecret' => C('APPSECRET'),
		));
        $this->getGroup();
    }
    public function getObj(){   //返回Wechat对象
        return $this->_weObj;
    }

    /**
     *获取分组信息
     */
    public  function getGroup(){
        $arr = $this->_weObj->getGroup();
        if($arr){
            foreach($arr['groups'] as $item){
                $index = $item['id'];
                $result[$index] = $item['name'];
            }
        }
        $this->_groupList = $result;
        return $this;
    }

    /**
     * 更改分组名称
     * @param string $groupName     原分组名称
     * @param string $name       新分组名称
     * @return boolean|array
     */
	public function updateGroup($groupName,$name){
		$id = $this->getGroupId($groupName);
        return $this->_weObj->updateGroup($id, $name);
    }

    /**
     * 新增自定分组
     * @param string $name 分组名称
     * @return boolean|array
     */
    public function createGroup($name){
        return $this->_weObj->createGroup($name);
    }

    /**
     * 获取用户所在分组
     * @param string $openId
     * @return bool|string  分组名称
     */
    public function getUserGroup($openId){
        $id = $this->_weObj->getUserGroup($openId);
        return $this->_groupList[$id];
    }

    /**
     * 移动用户分组
     * @param string $stuId 学号
     * @param string $openId
     * @return array|bool
     */
    public function updateGroupMembers($stuId,$openId){
		$groupName = $this->getGroupName($stuId);
		$id = $this->getGroupId($groupName);
        return $this->_weObj->updateGroupMembers($id, $openId);
    }

    /**
     * @return array分组列表
     */
    public function  getGroupList(){
        return $this->_groupList;
    }
	
	public function getGroupName($stuId){
		$str = substr($stuId,0,4);
		if(is_numeric($str)){
			return $str . '级';
		}
		return '未分组';
	}

	/*根据分组名得到分组id
	 * string
	 * return id int
	 */
	public function getGroupId($name){
		foreach($this->_groupList as $key => $value){
			if($value == $name){
				return $key;
			}
		}
		return 0;
	}
}
