<?php
/***************************************
*$File: app/bussiness/BRegion.php
*$Description:业务逻辑
*$Author: lideqiang
*$Time:  2015/4/18
****************************************/
class BRegion extends BRegionBase {

	public static $instance = NULL;
	public static function instance() {
		if(self::$instance == NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 根据拼音组织导入的地域数据
	 * @return multitype:multitype:
	 */
	public function navRegionByPinyin() {
		$return = array('heat' => array(), 'all' => array());

		$region = new Region();
		//组织有业务的热门城市
		$rows = $region->find(array('order' => 'heat desc', 'limit' => 35, 'status in(1,0)'));
		$heatRegion = array();
		foreach($rows as $row) {
			$heatRegion[$row->code] = array(
				'name' => $row->display_name,
				'pinyin' => $row->pinyin,
			);
		}
		$return['heat'] = $heatRegion;

		//按拼间首字母组织有业务城市
		$rows = $region->find(array('display_level' => '2', 'status=1'));
		$formatRows = array();
		foreach($rows as $row) {
			$first = substr($row->pinyin, 0, 1);
			if(!isset($formatRows[$first])) {
				$formatRows[$first] = array();
			}
			$formatRows[$first][$row->code] = array(
				'name' => $row->display_name,
				'pinyin' => $row->pinyin,
			);
		}
		ksort($formatRows);
		$return['all'] = $formatRows;
		return $return;
	}

}
