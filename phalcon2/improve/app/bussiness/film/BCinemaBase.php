<?php
/***************************************
*$File: app/business/BFilm.php
*$Description:业务逻辑
 *$Author: lideqiang
 *$Time:  2015/4/18
 ****************************************/
class BCinemaBase extends BussinessBase {

	/**
	 * 根据影片ID获取信息
	 * @param number $filmId
	 */
	public function getFilm($filmId = 0) {
		//$sql = "select * from ys_film f left join ys_film_heat h on f.id=h.film_id where f.status = 1 and f.id=:film_id";
		$sql = "select * from ys_film f left join ys_film_heat h on f.id=h.film_id where f.id=:film_id";
		return BalanceDb::instance()->fetchOne($sql, array('film_id' => $filmId), array('film_id' => PDO::PARAM_INT));
	}

	/**
	 * 根据影片某一天所有的排期
	 * @param number $filmId
	 * @param string $date
	 */
	public function getCinemaIdsByFilmIdAndDateAndRegion($filmId = 0, $date = '', $regions = array()) {
		$sql = "select cinema_id from ys_film_schedule where film_id=:film_id and date=:date";

		$bindParams = array();
		$bindParams['film_id'] = $filmId;
		$bindParams['date'] = $date;
		
		$bindTypes = array();
		$bindTypes['film_id'] = PDO::PARAM_INT;
		$bindTypes['date'] = PDO::PARAM_INT;

		if(isset($regions['province_code'])) {
			$sql .= " and province_code=:province_code";
			$bindParams['province_code'] = $regions['province_code'];
			$bindTypes['province_code'] = PDO::PARAM_STR;
		}
		if(isset($regions['city_code'])) {
			$sql .= " and city_code=:city_code";
			$bindParams['city_code'] = $regions['city_code'];
			$bindTypes['city_code'] = PDO::PARAM_STR;
		}
		if(isset($regions['county_code'])) {
			$sql .= " and county_code=:county_code";
			$bindParams['county_code'] = $regions['county_code'];
			$bindTypes['county_code'] = PDO::PARAM_STR;
		}

		$rows = BalanceDb::instance()->fetchAll($sql, $bindParams, $bindTypes);

		$cinemaIds = array();
		foreach($rows as $row) {
			$cinemaIds[] = $row['cinema_id'];
		}
		return $cinemaIds;
	}

	/**
	 * 获取某部电影在影院的某天的排期
	 * @param number $cinemaId
	 * @param number $filmId
	 * @param string $date
	 * @param unknown $regions
	 */
	public function getScheduleByFilmidAndCinemaidAndDateAndRegion($cinemaId = 0, $filmId = 0, $date = '', $regions = array()) {
		$sql = "select * from ys_film_schedule where cinema_id=:cinema_id and film_id=:film_id and date=:date";
		$bindParams = array();
		$bindParams['cinema_id'] = $cinemaId;
		$bindParams['film_id'] = $filmId;
		$bindParams['date'] = $date;

		$bindTypes = array();
		$bindTypes['cinema_id'] = PDO::PARAM_INT;
		$bindTypes['film_id'] = PDO::PARAM_INT;
		$bindTypes['date'] = PDO::PARAM_INT;

		if(isset($regions['province_code'])) {
			$sql .= " and province_code=:province_code";
			$bindParams['province_code'] = $regions['province_code'];
			$bindTypes['province_code'] = PDO::PARAM_STR;
		}
		if(isset($regions['city_code'])) {
			$sql .= " and city_code=:city_code";
			$bindParams['city_code'] = $regions['city_code'];
			$bindTypes['city_code'] = PDO::PARAM_STR;
		}
		if(isset($regions['county_code'])) {
			$sql .= " and county_code=:county_code";
			$bindParams['county_code'] = $regions['county_code'];
			$bindTypes['county_code'] = PDO::PARAM_STR;
		}

		return BalanceDb::instance()->fetchAll($sql, $bindParams, $bindTypes);
	}

	/**
	 * 
	 * @param unknown $ids
	 */
	public function getCinemasByIds($ids = array()) {
		if(empty($ids)) {
			$sql = "select * from ys_cinema limit 0, 5";
		} else {
			$sql = "select * from ys_cinema where id in(".implode(',', $ids).")";
		}

		return BalanceDb::instance()->fetchAll($sql);
	}
}
