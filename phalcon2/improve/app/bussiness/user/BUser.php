<?php
/***************************************
*$File: app/controllers/BUser.php
*$Description:
*$Author: lideqiang
*$Time:  2015/06/14
****************************************/
 class BUser extends BussinessBase {

	/**
	 * 单例模式，实例化对象
	 * @var type 
	 */
	public static $instance = NULL;

	/**
	 * 单例模式，实例化对象
	 * @return type
	 */
	public static function instance() {
		if(self::$instance == NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/*
	 * 获取帐号类型
	 * $param string $account 帐号
	 */
	public function getAccountType($account){
		if(ToolValidator::isEmail($account)){
			return 'email';
		}elseif(ToolValidator::isMobile($account)){
			return 'phone';
		}else{
			return false;
		}
	}

	/*
	 * 验证登录
	 * $param array $login 登录信息
	 */
	public function login($login){
		//检测系统中是否有该用户
		$filter[0] = "username = ".$login['username']." AND password = '".md5($login['password'])."'";
		$userInfo = User::instance()->findFirst($filter);
		if($userInfo) {
		   return $userInfo->toArray();
		} else {
		   return false;
		}
	}

	/*
	 * 登陆后会话信息处理、包括cookie
	 * $param array $useInfo 用户信息
	 * $param array $login 用户登录时的输入信息
	 */
	public function setLoginSession($userInfo, $login=array()) {
		//		@todo	此处不应该写cookie，直接通过session即可
		if(isset($login) && !empty($login)) {
			if($login['is_remember']) {
				setcookie('username',$login['username'],time()+15*24*3600);
			} else {
				setcookie('username',$login['username'],time()-15*24*3600);
			}
		}
		Phalcon\DI::getDefault()->getSession()->set("uid", $userInfo['id']);
		Phalcon\DI::getDefault()->getSession()->set("userInfo", $userInfo);
		return true;
	}

	/*
	 * 检查用户是否存在
	 * $param string $phone 手机号码
	 */
	public function phoneExists($phone = '') {
		$filter[0] = "phone = ?1";
		$filter['bind'] = array('1'=>$phone);
		$user = User::instance()->findFirst($filter);
		if($user) {
			return $user->toArray();
		}else{
			return false;
		}
	}

	/*
	 * 注册用户
	 * $param array $data 用户注册信息
	 */
	public function saveUser($data) {
		$data['password'] = md5($data['password']);
		unset($data['repassword']);
		$data['phone_verified'] = 1;
		$data['status'] = 1;
		$data['ctime'] = time();
		$data['utime'] = time();
		$sql = "insert into ys_user (`".implode('`,`', array_keys($data))."`) values('".implode("','", $data)."')";
		$result = BalanceDb::instance()->execute($sql);
		if($result) {
			$id = BalanceDb::instance()->lastInsertId();
			return true;
		} else {
			return false;
		}
	}

	 /*
	  * 检查是否登录
	  */
	public function checkLogin(){
	   $uid = Phalcon\Di::getDefault()->getSession()->get('uid');
		if(!$uid) {
			header("Location:'/user/login'");
			exit;
		}
	}

	/**
	 * 获取用户的安全等级
	 * @return array
	 */
	public function safeLevel() {
		$levels = array(
			1 => array('level' => 1, 'name' => '低'),
			2 => array('level' => 2, 'name' => '中'),
			3 => array('level' => 3, 'name' => '高'),
		);
		$user = Phalcon\Di::getDefault()->getSession()->get('userInfo');
		$level = 0;
		if($user['eamil_verified'] && $user['phone_verified']) {
			return $levels[3];
		} elseif(!$user['eamil_verified'] && !$user['phone_verified']) {
			return $levels[1];
		} else {
			return $levels[2];			
		}
	}

	/*
	 * 同ucenter验证登陆信息是否正确
	 */
	private function checkUcenter($login){
	   return true;//没有方法暂时不验证
	}

 }