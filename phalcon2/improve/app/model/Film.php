<?php
/***************************************
*$File: app/model/Film.php
*$Description:
*$Author: lideqiang
*$Time:  2015/06/09
****************************************/
class Film extends ModelBase {

	public static $instance = NULL;

    public function initialize() {
		$this->hasOne("id", "FilmHeat", "film_id");
		$this->hasMany("id", "FilmInfo", "film_id");
		parent::initialize();
		$this->TableNames = $this->getSource();
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

	public function DatasAdds($Arrays)
	{
		$SQLS_Fields = "";
		$SQLS_Values = "";
		foreach($Arrays as $Arrays_Keys=>$Arrays_Values){
			$SQLS_Fields .= ",".$Arrays_Keys;
			$SQLS_Values .= ",'".$Arrays_Values."'";
		}
		$SQLS = "INSERT INTO ".$this->TableNames."(".substr($SQLS_Fields,1).") VALUES(".substr($SQLS_Values,1).");";
		
		print_r($SQLS);exit;
	}

}
 