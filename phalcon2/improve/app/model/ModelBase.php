<?php

use \Phalcon\Mvc\Model;

class ModelBase extends Model
{
	protected $tablePrefix = 'ys_';

	public function initialize() {
		$this->setWriteConnectionService('db');
		$this->setReadConnectionService('db_read');
		$table = $this->tablePrefix.$this->getSource();
		$this->setSource($table);
	}

}
