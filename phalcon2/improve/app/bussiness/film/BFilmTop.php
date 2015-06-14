<?php

/***************************************
*$File: app/business/BFilmTop.php
*$Description:业务逻辑，共用模块
 *$Author: lideqiang
 *$Time:  2015/4/18
 ****************************************/
class BFilmTop extends BFilmBase
{

	/**
	 * 最热的10部影片
	 */
	public static function weekHot10() {
		$year = date('Y');
		$weekSequence = date('W');
		$rows = TopWeek::instance()->find(array('year' => $year, 'sequence' => $weekSequence, 'data_type' => 'hotFilm'));
		$filmIds = array();
		foreach($rows as $row) {
			$filmIds[] = $row->data_value;
		}
		$sql = "select * from ys_film f left join ys_film_heat h on f.id=h.film_id where f.id in(".implode(',', $filmIds).") ORDER BY h.score desc";
		$res= BalanceDb::instance()->fetchAll($sql);

		return $res;
	}

	/**
	 * 热门影评前5
	 */
	public static function critic5() {
		$sql = "select *,master_id as film_id from ys_news where master_type='critic' order by up desc limit 0,5";
		$rows = BalanceDb::instance()->fetchAll($sql);
		$films = array();
		foreach($rows as $row) {
			$films[$row['film_id']] = $row;
		}
		$sql = "select f.id as film_id,f.image,h.critic from ys_film f left join ys_film_heat h on f.id=h.film_id where f.id in(".implode(',', array_keys($films)).")";
		$rows = BalanceDb::instance()->fetchAll($sql);
		foreach($rows as $row) {
			$films[$row['film_id']]['critic'] = $row['critic'];
			$films[$row['film_id']]['image'] = $row['image'];
		}
		return $films;
	}
}
