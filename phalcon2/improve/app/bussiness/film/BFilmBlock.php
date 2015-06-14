<?php

/***************************************
*$File: app/business/BFilmBlock.php
*$Description:业务逻辑，共用模块
 *$Author: lideqiang
 *$Time:  2015/4/18
 ****************************************/
class BFilmBlock extends BFilmBase
{

	/**
	 * 热播影片
	 * @param number $page
	 * @param number $pagesize
	 * @return unknown
	 */
	public static function playing($page = 1, $pagesize = 10) {
		$page = $page > 1 ? $page : 1;
		$offset = $pagesize * ($page - 1);

		$bindParams = array();
		$bindParams['offset'] = $offset;
		$bindParams['pagesize'] = $pagesize;

		$bindTypes = array();
		$bindTypes['offset'] = PDO::PARAM_INT;
		$bindTypes['pagesize'] = PDO::PARAM_INT;

		$sql = "select * from ys_film f left join ys_film_heat h on f.id=h.film_id where f.status=1 order by h.score desc limit :offset,:pagesize";

		return BalanceDb::instance()->fetchAll($sql, $bindParams, $bindTypes);
	}

	/**
	 * 即将上映影片
	 * @param number $page
	 * @param number $pagesize
	 * @return unknown
	 */
	public static function upcomming($page = 1, $pagesize = 10) {
		$page = $page > 1 ? $page : 1;
		$offset = $pagesize * ($page - 1);

		$bindParams = array();
		$bindParams['offset'] = $offset;
		$bindParams['pagesize'] = $pagesize;

		$bindTypes = array();
		$bindTypes['offset'] = Pdo::PARAM_INT;
		$bindTypes['pagesize'] = Pdo::PARAM_INT;

		$sql = "select * from ys_film f left join ys_film_heat h on f.id=h.film_id where f.status=0 order by f.start_date desc limit :offset,:pagesize";
		return BalanceDb::instance()->fetchAll($sql, $bindParams, $bindTypes);
	}

	/**
	 * 根据地域获取影院
	 * @param array $regions
	 * @param number $page
	 * @param number $pagesize
	 */
	public static function cinemaByRegion($regions = array(), $page = 1, $pagesize = 10) {
		$page = $page > 1 ? $page : 1;
		$offset = $pagesize * ($page - 1);

		$bindParams = array();
		$bindParams['offset'] = $offset;
		$bindParams['pagesize'] = $pagesize;

		$bindTypes = array();
		$bindTypes['offset'] = Pdo::PARAM_INT;
		$bindTypes['pagesize'] = Pdo::PARAM_INT;

		$sql = "select * from ys_cinema c left join ys_cinema_heat h on c.id=h.cinema_id ";

		$wheres = array();
		if(isset($regions['province_code'])) {
			$wheres[] = "c.province_code=:province_code";
			$bindParams['province_code'] = $regions['province_code'];
			$bindTypes['province_code'] = PDO::PARAM_STR;
		}
		if(isset($regions['city_code'])) {
			$wheres[] = "c.city_code=:city_code";
			$bindParams['city_code'] = $regions['city_code'];
			$bindTypes['city_code'] = PDO::PARAM_STR;
		}
		if(isset($regions['county_code'])) {
			$wheres[] = "c.county_code=:county_code";
			$bindParams['county_code'] = $regions['county_code'];
			$bindTypes['county_code'] = PDO::PARAM_STR;
		}
		if($wheres) {
			$sql .= " where ".implode(" and ", $regions);
		}

		$sql .= " order by h.score desc limit :offset,:pagesize";

		return BalanceDb::instance()->fetchAll($sql, $bindParams, $bindTypes);
	}

}
