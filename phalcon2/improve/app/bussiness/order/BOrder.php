<?php

use Phalcon\DI;

/***************************************
*$File: app/business/BFilm.php
*$Description:业务逻辑
 *$Author: lideqiang
 *$Time:  2015/4/18
 ****************************************/
class BOrder 
{
	private $mOrder; // model实例

	/*
	 * 初始化一些信息
	 */
	function __construct()
	{
		$this->mOrder = new Order();
	}

}
