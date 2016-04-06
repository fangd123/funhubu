<?php
namespace Addon\Model;

class LibModel {
	protected $account;

	protected $snoopy;

	public function __construct(){
		$this->snoopy = new \Addon\Common\Snoopy();
	}

	//设置cookie
	public function setCookie($cookie){
		$this->snoopy->cookies["PHPSESSID"]=$cookie;
	}

	//是否需要输入姓名验证
	private function needValidate(){
		if(strpos($this->snoopy->lastredirectaddr,'redr_con.php'))
			return true;
		else
			return false;
	}

	//从数据库读帐号密码
	public function getAccount($userId){
		$stuLib = D('Addon/StuLib');
		$this->account = $stuLib->getAccount($userId);
	}

	//设置帐号密码
	public function setAccount($username, $password){
		$this->account = array(
			'username' => $username,
			'password' => $password,
		);
		return $this;
	}

	//登录到图书馆
	public function login(){
		$url="http://59.68.64.61:8080/reader/login.php";
		$this->snoopy->fetch($url);

		$cookie = $this->snoopy->headers[4];
		$cookie = substr($cookie,22,26);
		
		$this->snoopy->cookies["PHPSESSID"]=$cookie;
		$form['number']= $this->account['username'];
		$form['passwd']= $this->account['password'];
		$form['select']='cert_no';
		$url="http://59.68.64.61:8080/reader/redr_verify.php";
		$this->snoopy->submit($url,$form);
		return $this;
	}

	/**
	* 判断登录后的状态
	*/
	public function checkStatus() {
		if (strpos($this->snoopy->results,'对不起') !== false){
			return 'error';
		}else if($this->needValidate()){
			return 'validate';
		}
		return 'success';
	}

	//根据关键字查找图书
	public function searchBooks($keyword) {
		$url = "http://59.68.64.61:8080/opac/search_adv_result.php?sort=score&desc=true&sType0=02&q0=".$keyword;
		$this->snoopy->fetch($url);
		$html= str_get_html($this->snoopy->results);
		$a = $html->find('td[bgcolor="#FFFFFF"] a[class="blue"]');
		$td = $html->find('td[bgcolor="#FFFFFF"]');
		if(empty($a) || empty($td)){
			return array(
				'type' => 'text',
				'data' => '未找到这样的书',
			);
		}
		
		for ($i = 0;$i<10;$i++) {
			if(empty($a[$i]->href)){
				break;
			}
			$results[$i]['Url']= "http://59.68.64.61:8080/opac/".$a[$i]->href;
			$results[$i]['Title'] = $a[$i]->innertext;
			$results[$i]['real_title'] = $a[$i]->innertext;
			$results[$i]['number'] = $td[($i+1)*6-2]->innertext;
			$results[$i]['pubyear'] = $td[($i+1)*6-3]->innertext;
			$results[$i]['author'] = $td[($i+1)*6-4]->innertext;
			$results[$i]['Title'] = $results[$i]['Title']."\n".
				"作者：".$results[$i]['author']."\n".
				"出版：".$results[$i]['pubyear']."\n".
				"索书号：".$results[$i]['number'];

		}
		if(empty($results)){
			return array(
				'type' => 'text',
				'data' => '未找到任何结果',
			);
		}
		return array(
			'type' => 'news',
			'data' => $results,
		);
	}

	//查询当前借阅
	public function getBookRemind() {
		$url="http://59.68.64.61:8080/reader/book_lst.php";
		$this->snoopy->fetch($url);
		
		$html= str_get_html($this->snoopy->results);
		$a = $html->find('td[bgcolor="#FFFFFF"] a[class="blue"]');
		$td = $html->find('td[bgcolor="#FFFFFF"]');	
		$tr = $html->find('tr');
		$content ='';
		
		for ($i = 0;$i<count($tr)-1;$i++) {
			$results[$i]['Title'] = mb_convert_encoding($a[$i]->innertext, "utf8", "HTML-ENTITIES");
			$results[$i]['bookNumber'] = $td[$i*8]->plaintext;
			$results[$i]['borrowDate'] = $td[($i+1)*8-6]->plaintext;
			$results[$i]['returnDate'] = $td[($i+1)*8-5]->plaintext;
			$results[$i]['renewLink'] = 'http://59.68.64.61:8080/reader/ajax_renew.php?bar_code='.$results[$i]['bookNumber'].'&time='.time();
			$results[$i]['Title'] = $results[$i]['Title']."\n".
				"借阅日期：".$results[$i]['borrowDate']."\n".
				"应还日期：".$results[$i]['returnDate']."\n".
				"<a href=\"".U('Addon/Lib/renew@wx.0x2c.cn')."?cookie={$this->snoopy->cookies['PHPSESSID']}&booknum={$results[$i]['bookNumber']}\">续借</a>"."\n=============\n";
		}
		if(isset($results)){
			foreach($results as $i=>$value) {
				$content .= $results[$i]['Title']."\n";
			}
		}
		
		if(empty($content)){
			$content = '您没有借阅书籍';
		}
		return array(
			'type' => 'text',
			'data' => $content,
		);
	}

	//获取借阅历史
	public function getBorrowList(){
		$url = 'http://59.68.64.61:8080/reader/book_hist.php';
		$form['para_string'] = 'all';
		$this->snoopy->submit($url,$form);
		$html= str_get_html($this->snoopy->results);
		$table = $html->find('table',0);
		if(empty($table)){
			return;
		}

		$count = count($table->find('tr'));
		$data = array();
		for($i = 1; $i < $count; $i++){
			$row = $table->find('tr',$i);
			$data[] = array(
				'book_id' => $row->find('td',1)->plaintext,
				'borrow_date' => $row->find('td',4)->plaintext,
				'return_date' => $row->find('td',5)->plaintext,
				'place' => $row->find('td',6)->plaintext,
			);
		}
		return $data;
	}

	public function validate($name){
		$url = 'http://59.68.64.61:8080/reader/redr_con_result.php';
		$form['name'] = $name;
		$this->snoopy->submit($url,$form);

		if (strpos($this->snoopy->results,'身份验证失败') !== false){
			return false;
		}
		return true;
	}

}

