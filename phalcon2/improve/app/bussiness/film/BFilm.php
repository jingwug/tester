<?php

use Phalcon\DI;

/***************************************
*$File: app/business/BFilm.php
*$Description:业务逻辑
 *$Author: lideqiang
 *$Time:  2015/4/18
 ****************************************/
class BFilm extends BFilmBase
{
	private $mFilm; // model实例

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

	
	/*
	 * 初始化一些信息
	 */
	function __construct() {
		$this->mFilm = new Film ();
	}
	
	/*
	 * 获取影片 $param array $filter 筛选条件
	 */
	public function getFilms($filter) {
		$films = $this->mFilm->find($filter);
		$filmsArr = array();
		if ($films) {
			foreach($films as $film) {
				$filmsArr [] = $this->getRelative ( $film );
			}
		} else {
			return false;
		}
		return $filmsArr;
	}
	
	/*
	 * 整合影片相关信息 $param object $film 查询结果
	 */
	public function getRelative($film) {
		$filmArr = $film->toArray ();
		// 获取影片热度、扩展属性
		
		$filmHeat = $film->getFilmHeat ();
		$filmInfo = $film->getFilmInfo ();
		if ($filmHeat) {
			$filmArr ['filmHeat'] = $filmHeat->toArray ();
		}
		if ($filmInfo) {
			$filmArr ['filmInfo'] = $filmInfo->toArray ();
		}

		$filmArr ['filmHeat'] ['attention'] = @$filmArr ['filmHeat'] ['attention'] ? $filmArr ['filmHeat'] ['attention'] : 0;
		$filmArr ['filmHeat'] ['audience'] = @$filmArr ['filmHeat'] ['audience'] ? $filmArr ['filmHeat'] ['audience'] : 0;
		// 上映日期处理
		$filmArr ['start_date'] = ToolDate::getShortDate ( $filmArr ['start_date'] );
		$filmArr ['end_date'] = ToolDate::getShortDate ( $filmArr ['end_date'] );
		return $filmArr;
	}

	/*
	 * 按分页获取影片 $param array $filter 筛选条件
	 */
	public function getPageFilms($filter, $size = 10, $current = 1) {
		$films = $this->mFilm->find ( $filter );
		return $films;
	}
	
	/*
	 * 获取地区热映影片
	 * $param int $city_code 城市编码
	 * $param int $status 影片状态 1：上架 -1：下架 0:预告
	 */
	public function getCinemaFilms($city_code, $status) {
		$date_time = date("Ymd");
		$data = array(
				"date = $date_time AND city_code = $city_code"
		);
		if (! isset ( $data ['group'] ) || empty ( $data ['group'] )) {
			$data ['group'] = "film_id";
		}
		$cinemaFilms = CinemaFilm::find ( $data );
		$cinemaFilmsArr = array ();
		// 电影详情查询条件数组
		if ($cinemaFilms) {
			foreach ( $cinemaFilms as $cinemaFilm ) {
				if ($film_info = $this->getFilms ( array (
						"status = $status AND id = $cinemaFilm->film_id" 
				) )) {
					$cinemaFilmsArr [] = $film_info [0];
				}
			}
		} else {
			return false;
		}
		return $cinemaFilmsArr;
	}
	
	/*
	 * 获取影片属性和类型
	 */
	public function getFilmType() {
		$filmType = FilmAttribute::find ();
		$result = array ();
		foreach ( $filmType as $vInfo ) {
			$result [$vInfo->attribute] [] = $vInfo->value;
		}
		return $result;
	}
	
	/*
	 * 最受关注电影排行榜（取未上映）
	 */
	public function getAttenFlims() {
		$sql_str = ' WHERE f.status = 0';
		$sql_order = ' ORDER BY fh.`attention` DESC limit 10';
		$sql = "SELECT f.id,f.name,f.image,f.start_date,fh.attention,fh.score FROM ys_film f
            LEFT JOIN ys_film_heat fh ON f.id=fh.film_id" . $sql_str . " GROUP BY f.id" . $sql_order;
		return BalanceDb::instance()->fetchAll ( $sql );
	}
	
	/**
	 * 重组电影的信息 只保留二维数组
	 */
	public function getFilmArray(&$data) {
		foreach ( $data as &$v ) {
			if (isset ( $v ['filmHeat'] ) && ! empty ( $v ['filmHeat'] )) {
				$v ['cinemas'] = $v ['filmHeat'] ['cinemas'];
				$v ['attention'] = $v ['filmHeat'] ['attention'];
				$v ['audience'] = $v ['filmHeat'] ['audience'];
				$v ['score'] = $v ['filmHeat'] ['score'];
				$v ['screen'] = $v ['filmHeat'] ['screen'];
				$v ['amount'] = $v ['filmHeat'] ['amount'];
				$v ['amount_total'] = $v ['filmHeat'] ['amount_total'];
			}
			if (isset ( $v ['filmInfo'] ) && ! empty ( $v ['filmInfo'] )) {
				foreach ( $v ['filmInfo'] as $vInfo ) {
					if (isset ( $v [$vInfo ['attribute']] ) && ! empty ( $v [$vInfo ['attribute']] )) {
						$v [$vInfo ['attribute']] .= ' ' . $vInfo ['data'];
					} else {
						$v [$vInfo ['attribute']] = $vInfo ['data'];
					}
				}
			}
			unset ( $v ['filmHeat'] );
			unset ( $v ['filmInfo'] );
		}
	}

	/**
	 * 获取即将上映的影片信息
	 * 
	 * @param number $page        	
	 * @param number $pagesize        	
	 */
	public function getPreviewFilms($page = 1, $pagesize = 8) {
		$page = $page > 1 ? $page : 1;
		$offset = $pagesize * ($page - 1);

		$sql = "select *,f.id as fid from ys_film f left join ys_film_heat h on f.id=h.film_id where f.status=0 order by f.start_date desc limit :offset,:pagesize";
		return BalanceDb::instance()->fetchAll(
				$sql, 
				array('offset' => $offset, 'pagesize' => $pagesize), 
				array('offset' => PDO::PARAM_INT, 'pagesize' => PDO::PARAM_INT)
			);
	}

	/**
	 * 统计总数
	 */
	public function countPreviewFilms() {
		$sql = "select count(1) as total from ys_film f left join ys_film_heat h on f.id=h.film_id where f.status=0";
		$row = BalanceDb::instance()->fetchOne($sql);
		return $row ? $row['total'] : 0; 
	}

	/**
	 * 最热预告
	 * @param number $page
	 * @param number $pagesize
	 * @return unknown
	 */
	public function getHeatPreviewFilms($page = 1, $pagesize = 8) {
		$page = $page > 1 ? $page : 1;
		$offset = $pagesize * ($page - 1);

		$sql = "select * ,f.id as fid from ys_film f left join ys_film_heat h on f.id=h.film_id where f.status=0 order by f.start_date desc limit :offset,:pagesize";
		return BalanceDb::instance()->fetchAll(
				$sql, 
				array('offset' => $offset, 'pagesize' => $pagesize), 
				array('offset' => PDO::PARAM_INT, 'pagesize' => PDO::PARAM_INT)
			);
	}

	/**
	 * 统计最热预告数
	 * @return Ambigous <number, unknown>
	 */
	public function countHeatPreviewFilms() {
		$sql = "select count(1) as total from ys_film f left join ys_film_heat h on f.id=h.film_id where f.status=0";
		$row = BalanceDb::instance()->fetchOne($sql);
		return $row ? $row['total'] : 0;
	}

}
