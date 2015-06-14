<?php
/***************************************
*$File: app/controllers/Comment.php
*$Description:
*$Author: KongSeng
*$Time:  2015/4/18
****************************************/
class TopWeek extends ModelBase
{
	public static $instance = NULL;

    public function initialize() {
        parent::initialize();
    }

	/**
	 * 单例模式，实例化对象
	 */
	public static function instance() {
		if(self::$instance == NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}
