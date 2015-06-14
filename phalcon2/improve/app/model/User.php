<?php

class User extends ModelBase
{

	/**
	 *单例模式，静态变量
	 * @var type 
	 */
	public static $instance = NULL;

	/**
	 * 对象初始化
	 */
	public function initialize() {
		parent::initialize();
        $this->skipAttributesOnCreate(array('nickname','email','uid','qq','email_verified','headimg','sex'));
        $this->skipAttributesOnUpdate(array('uid'));
	}

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

}
