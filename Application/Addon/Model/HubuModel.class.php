<?php
namespace Addon\Model;

class HubuModel {
    private $snoopy;//Snoopy对象
    private $form;//登陆表单数据
    private $results;//提交表单返回的结果

    /**
     * 初始化$snoopy
     */
    public function __construct(){
        $this->snoopy = new \Addon\Common\Snoopy();
    }

    /**
     * 获取cookie
     * @return string $cookie
     */
    public function getCookie(){
		try{
			$url="http://jwxt.hubu.edu.cn/";
			$this->snoopy->fetch($url);
			$cookie = $this->snoopy->headers[9];//从header中截取cookie
			$cookie = substr($cookie,23,32);
			$this->snoopy->cookies["JSESSIONID"]=$cookie;
		}catch(Exception $e){
			exit;
		}
        return $cookie;
    }

    /**
     *设置cookie
     * @param $cookie
     */
    public function setCookie($cookie){
        $this->snoopy->cookies["JSESSIONID"]=$cookie;
		return $this;
    }

    /**
     * 获取验证码，返回验证码图片
     * @return string
     */
    public function getVerCodePic(){
		try{
			$url = "http://jwxt.hubu.edu.cn/verifycode.servlet";
			$this->snoopy->fetch($url);
		}catch(Exception $e){
			exit;
		}
        return $this->snoopy->results;
    }

    /**
     * 根据要填写的字段构造登陆表单（湖北大学只需要一下三个字段即可登陆）
     * @param $stId
     * @param $password
     * @param $code
     */
    public function setForm($stuId,$password,$code){
        $this->form['USERNAME'] = $stuId;
        $this->form['PASSWORD']= $password;
        $this->form['RANDOMCODE'] = $code;
		return $this;
    }

    /**
     * 提交表单登录教务处管理系统
     */
    public function logon(){
        $url="http://jwxt.hubu.edu.cn/Logon.do?method=logon";

		try{
			$this->snoopy->submit($url,$this->form);
			$this->results = $this->snoopy->results;
			$url="http://jwxt.hubu.edu.cn/Logon.do?method=logonBySSO";
			$this->snoopy->submit($url,$form);
		}catch(Exception $e){
			exit;
		}
		return $this;
    }

    /**
     *检查提交表单后的结果，返回相应的状态码
     * @return int
     */
    public function checkStuInfo(){
		$result['status'] = 'success';
		$info = array(
			'该帐号不存在或密码错误',
			'验证码错误',
			'用户名或密码不能为空',
			'出错页面',
		);
		if(empty($this->results)){
			$result['status'] = 'error';
			$result['code'] = '疑似非法操作';
		}
		foreach($info as $value){
			if(strpos($this->results,$value)){
				$result['status'] = 'error';
				$result['code'] = $value;
				break;
			}
		}

		return $result;
    }

    /**
     * 从教务处获取指定学期课程信息
     * @param $semester 学期
     * @param $stuId 学号
     * @return string
     */
    public function getCourse($semester, $stuId){
        $url="http://jwxt.hubu.edu.cn//tkglAction.do?method=goListKbByXs&sql=&xnxqh={$semester}&zc=&xs0101id={$stuId}";

        try{
			$this->snoopy->fetch($url);
		}catch(Exception $e){
			return array();
		}
		//如果个人课表未公布则从全校课表获取数据
		if(strpos($this->snoopy->results,'课表暂未公布，不能课表查看')){
			$url = "http://jwxt.hubu.edu.cn/jiaowu/pkgl/llsykb/llsykb_kb.jsp";
			$name =M('Stu')->join('__USER__ ON __STU__.user_id = __USER__.id')
				->where(array('open_id'=>I('get.open_id')))->getField('name');
			$form = array(
				'type' => 'xs0101',
				'isview' => 1,
				'zc' => '',
				'xnxq01id' => $semester,
				'xs0101xm' => $name,
				'xs0101id' => $stuId,
			);
			$this->snoopy->submit($url, $form);
		}

		return $this->snoopy->results;
    }


    /**
     *获取指定学期的成绩
     * @param $semester
     * @return string
     */
    public function getGrade($semester, $stuId){
        $contentStr = '';
        $time = time()."000";
        $url ="http://jwxt.hubu.edu.cn/jiaowu/cjgl/xszq/query_xscj.jsp?tktime=".$time;
        try{
			$this->snoopy->fetch($url);
			$form1['xsfs'] = "qbcj";
			$form1['kksj'] = $semester;
			$url ="http://jwxt.hubu.edu.cn/xszqcjglAction.do?method=queryxscj";
			$this->snoopy->submit($url,$form1);
		}catch(Exception $e){
			return array();
		}
		return $this->snoopy->results;
    }

    /**
     * 查询当前学期考试安排
     * @param $semester
     * @return array
     */
    public function getExam($semester, $stuId){
        
        $form1['xnxqh'] = $semester;
        $url ="http://jwxt.hubu.edu.cn/kwsjglAction.do?method=sosoXsFb";
        try{
			$this->snoopy->submit($url,$form1);
		}catch(Exception $e){
			return array();
		}
		return $this->snoopy->results;
    }

    /**
     *验证session是否有效
     * @param $session
     * @return bool
     */
    public function validateSession($session){//随意访问一个链接，如果出错则表明session失效
        $this->snoopy->cookies["JSESSIONID"]=$session;
        $url ="http://jwxt.hubu.edu.cn/jiaowu/kwgl/kwgl_xsJgfb_soso.jsp?tktime=";//考试安排查询接口
        try{
			$this->snoopy->fetch($url);
		}catch(Exception $e){
			return false;
		}

        if(strpos($this->snoopy->results,'出错页面')){
            return false;
        }else{
			$this->results = true;
            return true;
        }
    }

    /**
     * 获取院系和专业
     * @return array
     */
    public function getClass(){
        $time = time()."000";
        $url ="http://jwxt.hubu.edu.cn/xszhxxAction.do?method=addStudentPic&tktime=".$time;
        try{
			$this->snoopy->fetch($url);
		}catch(Exception $e){
			return;
		}
		return $this->snoopy->results;
    }
} 
