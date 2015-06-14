<?php
/***************************************
*$File: app/business/BFilm.php
*$Description:业务逻辑
 *$Author: lideqiang
 *$Time:  2015/4/18
 ****************************************/
class BCinema extends BCinemaBase {

	//默认页面大小
	const PAGESIZE = 20;

	//实例化对象
	public static $instance = NULL;

	/**
	 * 实例化类，单例模式
	 * @return BFilm
	 */
	public static function instance() {
		if(self::$instance == NULL) {
			self::$instance = new self();
		}
		return self::$instance;
	}

	/**
	 * 统计影院总数
	 * @param type $params
	 * @return type
	 */
	public static function countByWordRegionEffectParkRefundActive($params = array()) {
		return self::findAllByWordRegionEffectParkRefundActive($params, true);
	}

	/**
	 * 查询影院
	 * $params = array(
	 *	'keyword' => '',
	 *	'city_code' => '',		//城市编码
	 *	'county_code' => '',	//县区编码
	 *	'screen_effect' => '',	//特效厅
	 *  'parking' => '',		//停车
	 * 'is_refund' => '',		//是否退票
	 * 'have_activity' => '',	//是否有活动
	 * 'page' => 1,
	 * 'pagesize' => 20,
	 * );
	 * @param type $params
	 * @param type $isCount
	 * @return boolean
	 */
	public static function findAllByWordRegionEffectParkRefundActive($params = array(), $isCount = false) {
		if($isCount) {
			$sql = "select count(1) as total from ys_cinema c";
		} else {
			$sql = "select *,c.id as cinema_id from ys_cinema c left join ys_cinema_heat h on c.id=h.cinema_id";
		}
		$wheres = array();
		//关键词
		if(isset($params['name'])) {
			$params['name'] = trim($params['name']);
			if(!empty($params['name'])) {
				$wheres[] = "c.name like :name";
				$params['name'] = '%'.$params['name'].'%';
			}
		}
		//地域查询
		if(isset($params['city_code']) && !empty($params['city_code'])) {
			$wheres[] = 'c.city_code=:city_code';
		}
		if(isset($params['county_code']) && !empty($params['county_code'])) {
			$wheres[] = 'c.county_code=:county_code';
		}
		//特效厅查询
		if(isset($params['screen_effect']) && !empty($params['screen_effect'])) {
			$wheres[] = "c.screen_effect like :screen_effect";
			$params['screen_effect'] = '%'.$params['screen_effect'].'%';
		}
		//是否可停车
		if(isset($params['parking']) && !empty($params['parking'])) {
			$wheres[] = 'c.parking=:parking';
		}
		//是否可退款
		if(isset($params['is_refund']) && !empty($params['is_refund'])) {
			$wheres[] = 'c.is_refund=:is_refund';
		}
		//是否有活动
		if(isset($params['have_activity']) && !empty($params['have_activity'])) {
			$wheres[] = 'c.have_activity=:have_activity';
		}
		
		if(empty($wheres)) {
			return false;
		}

		if($isCount) {
			if(isset($params['page'])) {
				unset($params['page']);
			}
			if(isset($params['pagesize'])) {
				unset($params['pagesize']);
			}
			if(isset($params['order'])) {
				unset($params['order']);
			}
			$sql = $sql." where ".implode(" AND ", $wheres);
			$result = BalanceDb::instance()->fetchAll($sql, $params);
			return $result ? $result[0]['total'] : false;
		} else {
			$page = 1;
			$pagesize = self::PAGESIZE;
			if(isset($params['page'])) {
				if(intval($params['page']) > 0) {
					$page = (int)$params['page'];
				}
				unset($params['page']);
			}
			if(isset($params['pagesize'])) {
				$pagesize = (int)$params['pagesize'];
				unset($params['pagesize']);
			}
			$sql .= " where ".implode(" AND ", $wheres);
			if(isset($params['order'])) {
				$sql .= " order by h.{$params['order']}";
				unset($params['order']);
			}
			$limit = (($page - 1) * $pagesize).",".$pagesize;
			$sql .= " LIMIT {$limit}";
			return BalanceDb::instance()->fetchAll($sql, $params);
		}
	}

	/**
	 * 统计影院当天正在放映的影片总数
	 * @param type $cinemaid
	 * @param type $date
	 * @param type $startTime
	 * @param type $isCount
	 * @return type
	 */
	public static function countPlayingScheduleByCinemaidDateStarttime($cinemaid = '', $date = '', $startTime = '') {
		return self::findPlayingFilmsByCinemaidDateStarttime($cinemaid, $date, $startTime, true);
	}

	/**
	 * 查询正在播放的影片
	 * @param type $cinemaid
	 * @param type $date
	 */
	public static function findPlayingScheduleByCinemaidDateStarttime($cinemaid = '', $date = '', $startTime = '', $isCount = false) {
		$cinemaid = (int)$cinemaid;
		$date = (int)$date;
		$date = empty($date) ? date('Ymd') : $date;
		$startTime = empty($startTime) ? time() : $startTime;
		if(empty($cinemaid)) return false;
		
		$params = array(
			'cinema_id' => $cinemaid,
			'date' => $date,
			'start_time' => $startTime,
		);
		if($isCount) {
			$sql = "select count(1) as total from ys_film_schedule where cinema_id=:cinema_id and date=:date and start_time>:start_time";
			return empty($result) ? 0 : $result[0]['total'];
		} else {
			$sql = "select * from ys_film_schedule where cinema_id=:cinema_id and date=:date and start_time>:start_time";
			return BalanceDb::instance()->fetchAll($sql, $params);			
		}
	}

	/**
	 * 统计影院当天放映的总数
	 * @param type $cinemaid
	 * @param type $date
	 * @return type
	 */
	public static function countScheduleByCinemaidDate($cinemaid = '', $date = '') {
		return self::findFilmsByCinemaidDate($cinemaid, $date, true);
	}

	/**
	 * 查询某影院某天所有的影片
	 * @param type $cinemaid
	 * @param type $date
	 */
	public static function findScheduleByCinemaidDate($cinemaid = '', $date = '', $isCount = false) {
		$cinemaid = (int)$cinemaid;
		$date = (int)$date;
		$date = empty($date) ? date('Ymd') : $date;
		if(empty($cinemaid)) return false;

		$params = array(
			'cinema_id' => $cinemaid,
			'date' => $date,
		);
		if($isCount) {
			$sql = "select count(1) as total from ys_film_schedule where cinema_id=:cinema_id and date=:date";
			$result = BalanceDb::instance()->fetchAll($sql, $params);
			return empty($result) ? 0 : $result[0]['total'];
		} else {
			$sql = "select * from ys_film_schedule where cinema_id=:cinema_id and date=:date";
			return BalanceDb::instance()->fetchAll($sql, $params);
		}
	}

	/**
	 * 统计影院当天已经播放的场次
	 * @param type $cinemaid
	 * @param type $date
	 * @param type $startTime
	 * @return type
	 */
	public static function countPlayedScheduleByCinemaidDate($cinemaid = '', $date = '', $startTime = '') {
		return self::findPlayedFilmsByCinemaidDate($cinemaid, $date, $startTime, true);
	}

	/**
	 * 查询影院某天未播放的影片
	 * @param type $cinemaid
	 * @param type $date
	 */
	public static function findPlayedScheduleByCinemaidDate($cinemaid = '', $date = '', $startTime = '', $isCount = false) {
		$cinemaid = (int)$cinemaid;
		$date = (int)$date;
		$date = empty($date) ? date('Ymd') : $date;
		$startTime = empty($startTime) ? time() : $startTime;
		if(empty($cinemaid)) return false;

		$params = array(
			'cinema_id' => $cinemaid,
			'date' => $date,
			'start_time' => $startTime,
		);
		if($isCount) {
			$sql = "select * from ys_film_schedule where cinema_id=:cinema_id and date=:date and start_time<:start_time";
			$result = BalanceDb::instance()->fetchAll($sql, $params);
			return empty($result) ? 0 : $result[0]['total'];
		} else {
			$sql = "select * from ys_film_schedule where cinema_id=:cinema_id and date=:date and start_time<:start_time";
			return BalanceDb::instance()->fetchAll($sql, $params);
		}
	}

}
